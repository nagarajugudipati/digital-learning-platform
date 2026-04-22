<?php

namespace App\Services;

use App\Models\ChatbotQA;
use App\Models\Lesson;

class ChatbotService
{
    private array $knowledgeBase;
    private array $greetings;
    private array $farewells;

    public function __construct()
    {
        $this->greetings = ['hello', 'hi', 'hey', 'namaste', 'namaskar', 'good morning', 'good afternoon', 'good evening', 'sat sri akal'];
        $this->farewells = ['bye', 'goodbye', 'see you', 'alvida', 'thank you', 'thanks', 'dhanyawad'];
        $this->knowledgeBase = $this->buildKnowledgeBase();
    }

    public function respond(string $userMessage): array
    {
        // Normalize: lowercase, strip non-alphanumeric (keep spaces), collapse spaces
        $raw     = strtolower(trim($userMessage));
        $message = preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9\s]/', ' ', $raw));

        if ($this->isGreeting($raw)) {
            return [
                'response'   => "Namaste! 🙏 I'm your AI study assistant for Nabha Digital School.\n\nI can help you with **Mathematics, Science, English, Hindi,** and **Social Studies**.\n\nTry asking:\n• \"What is Newton's Laws?\"\n• \"Explain photosynthesis\"\n• \"What are fractions?\"\n\nOr type **help** to see all topics!",
                'intent'     => 'greeting',
                'subject'    => null,
                'confidence' => 1.0,
            ];
        }

        if ($this->isFarewell($raw)) {
            return [
                'response'   => "Goodbye! Keep learning and stay curious! 📚\n\nRemember, every question you ask brings you closer to knowledge. Jai Hind! 🇮🇳",
                'intent'     => 'farewell',
                'subject'    => null,
                'confidence' => 1.0,
            ];
        }

        if ($this->isHelpRequest($raw)) {
            return [
                'response'   => "I can help you with:\n\n📐 **Mathematics** — Pythagoras, Fractions, Algebra, HCF/LCM, Percentages, Geometry\n🔬 **Science** — Newton's Laws, Photosynthesis, Cell Biology, Periodic Table\n📖 **English** — Tenses, Parts of Speech, Grammar\n🗣️ **Hindi** — Swar, Vyanjan, Varna Mala\n🗺️ **Social Studies** — Punjab, Indian Constitution, Independence\n\n**Example questions:**\n• \"What is photosynthesis?\"\n• \"Explain Pythagoras theorem\"\n• \"Newton's laws of motion\"\n• \"What is the capital of Punjab?\"",
                'intent'     => 'help',
                'subject'    => null,
                'confidence' => 1.0,
            ];
        }

        // ── Priority 1: DB-trained Q&A (admin / teacher additions) ──────────────
        $dbQa = $this->searchDatabase($message);
        if ($dbQa) {
            return $dbQa;
        }

        // ── Priority 2: Built-in knowledge base ──────────────────────────────────
        $bestMatch = $this->findBestMatch($message);
        if ($bestMatch['confidence'] >= 0.2) {
            return $bestMatch;
        }

        // ── Priority 3: Lesson title search ──────────────────────────────────────
        $dbResult = $this->searchLessons($raw);
        if ($dbResult) {
            return $dbResult;
        }

        // ── Final fallback with suggestions ──────────────────────────────────────
        return [
            'response'   => "I couldn't find a specific answer for \"" . $userMessage . "\".\n\n📚 **Try asking about:**\n• Newton's Laws of Motion\n• Photosynthesis\n• Pythagoras Theorem\n• Fractions & HCF/LCM\n• Indian Constitution\n• Punjab Geography\n\n💡 Type **help** for a full topic list, or check the **Lessons** section — your teacher may have uploaded something on this!",
            'intent'     => 'unknown',
            'subject'    => null,
            'confidence' => 0.0,
        ];
    }

    // ─── DB Q&A search (trained by admin / teacher) ───────────────────────────

    private function searchDatabase(string $normalized): ?array
    {
        // 1. Phrase match against question or keywords column
        $qa = ChatbotQA::where('question', 'LIKE', "%{$normalized}%")
            ->orWhere('keywords', 'LIKE', "%{$normalized}%")
            ->first();

        // 2. Word-by-word fallback (tries each significant word individually)
        if (!$qa) {
            $words = array_filter(explode(' ', $normalized), fn ($w) => strlen($w) > 3);
            foreach ($words as $word) {
                $qa = ChatbotQA::where('question', 'LIKE', "%{$word}%")
                    ->orWhere('keywords', 'LIKE', "%{$word}%")
                    ->first();
                if ($qa) break;
            }
        }

        if (!$qa) return null;

        return [
            'response'   => $qa->answer,
            'intent'     => 'db_qa',
            'subject'    => null,
            'confidence' => 0.9,
        ];
    }

    // ─── Database lesson search ───────────────────────────────────────────────

    private function searchLessons(string $rawInput): ?array
    {
        // Search published lessons whose title or description matches the query
        $lesson = Lesson::where('status', 'published')
            ->where(function ($q) use ($rawInput) {
                $q->where('title', 'LIKE', "%{$rawInput}%")
                  ->orWhere('description', 'LIKE', "%{$rawInput}%");
            })
            ->first();

        if (!$lesson) {
            // Try word-by-word: if any significant word (>4 chars) matches a lesson title
            $words = array_filter(explode(' ', $rawInput), fn ($w) => strlen($w) > 4);
            foreach ($words as $word) {
                $lesson = Lesson::where('status', 'published')
                    ->where('title', 'LIKE', "%{$word}%")
                    ->first();
                if ($lesson) break;
            }
        }

        if (!$lesson) return null;

        return [
            'response'   => "📖 I found a relevant lesson in your course material!\n\n**\"" . $lesson->title . "\"**\n" .
                            ($lesson->description ? "\n" . \Illuminate\Support\Str::limit($lesson->description, 200) . "\n" : '') .
                            "\n👉 Check your **Lessons** section to view the full content!",
            'intent'     => 'lesson_found',
            'subject'    => $lesson->subject ?? null,
            'confidence' => 0.6,
        ];
    }

    // ─── Matching ─────────────────────────────────────────────────────────────

    private function findBestMatch(string $message): array
    {
        $bestScore = 0;
        $bestEntry = null;

        foreach ($this->knowledgeBase as $entry) {
            $score = $this->calculateScore($message, $entry['keywords']);
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestEntry = $entry;
            }
        }

        if ($bestEntry && $bestScore >= 0.2) {
            return [
                'response'   => $bestEntry['response'],
                'intent'     => $bestEntry['intent'],
                'subject'    => $bestEntry['subject'],
                'confidence' => $bestScore,
            ];
        }

        return ['response' => '', 'intent' => 'unknown', 'subject' => null, 'confidence' => 0.0];
    }

    private function calculateScore(string $message, array $keywords): float
    {
        $matched     = 0;
        $totalWeight = 0;

        foreach ($keywords as $keyword => $weight) {
            $totalWeight += $weight;
            if (str_contains($message, $keyword)) {
                $matched += $weight;
            }
        }

        if ($totalWeight === 0) return 0;
        return min($matched / $totalWeight, 1.0);
    }

    // ─── Intent detectors ────────────────────────────────────────────────────

    private function isGreeting(string $message): bool
    {
        foreach ($this->greetings as $g) {
            if (str_contains($message, $g)) return true;
        }
        return false;
    }

    private function isFarewell(string $message): bool
    {
        foreach ($this->farewells as $f) {
            if (str_contains($message, $f)) return true;
        }
        return false;
    }

    private function isHelpRequest(string $message): bool
    {
        $helpTriggers = ['help', 'what can you do', 'what do you know', 'topics', 'subjects', 'what can you answer'];
        foreach ($helpTriggers as $t) {
            if (str_contains($message, $t)) return true;
        }
        return false;
    }

    // ─── Knowledge Base ───────────────────────────────────────────────────────

    private function buildKnowledgeBase(): array
    {
        return [

            // ═══════════════════════════ MATHEMATICS ═══════════════════════════

            [
                'subject' => 'mathematics',
                'intent'  => 'math_pythagoras',
                'keywords' => ['pythagoras' => 3, 'pythagorean' => 3, 'theorem' => 1, 'hypotenuse' => 2, 'right triangle' => 2, 'right angle triangle' => 2, 'right angle' => 1],
                'response' => "📐 **Pythagoras Theorem**\n\nIn a right-angled triangle:\n**a² + b² = c²**\n\nWhere:\n• **c** = hypotenuse (longest side, opposite the right angle)\n• **a** and **b** = the other two sides\n\n**Example:** If a = 3, b = 4:\nc = √(3² + 4²) = √(9 + 16) = √25 = **5**\n\n📌 This is called a **3-4-5 Pythagorean Triple**!\n\n💡 *The square on the hypotenuse equals the sum of squares on the other two sides.*",
            ],
            [
                'subject' => 'mathematics',
                'intent'  => 'math_fraction',
                'keywords' => ['fraction' => 3, 'fractions' => 3, 'numerator' => 2, 'denominator' => 2, 'proper fraction' => 2, 'improper' => 1, 'mixed number' => 1],
                'response' => "🔢 **Fractions**\n\nA fraction represents a **part of a whole**: **Numerator / Denominator**\n\n**Types:**\n• **Proper fraction**: Numerator < Denominator (e.g., 3/4)\n• **Improper fraction**: Numerator > Denominator (e.g., 5/3)\n• **Mixed number**: Whole + Fraction (e.g., 1½)\n\n**Operations:**\n• **Add/Subtract**: Make denominators equal first\n• **Multiply**: Numerator × Numerator, Denominator × Denominator\n• **Divide**: Multiply by the reciprocal (flip the second fraction)\n\n💡 *To simplify: divide both numerator and denominator by their HCF*",
            ],
            [
                'subject' => 'mathematics',
                'intent'  => 'math_hcf_lcm',
                'keywords' => ['hcf' => 3, 'lcm' => 3, 'highest common factor' => 3, 'lowest common multiple' => 3, 'gcd' => 2, 'common factor' => 1, 'common multiple' => 1],
                'response' => "🔢 **HCF and LCM**\n\n**HCF (Highest Common Factor):**\nThe largest number that divides two or more numbers exactly.\n*Example: HCF of 12 and 18 = 6*\n\n**LCM (Lowest Common Multiple):**\nThe smallest number divisible by two or more numbers.\n*Example: LCM of 4 and 6 = 12*\n\n**Key Relationship:** HCF × LCM = Product of the two numbers\n\n**Prime Factorization Method:**\n12 = 2² × 3 | 18 = 2 × 3²\nHCF = 2 × 3 = **6** | LCM = 2² × 3² = **36**",
            ],
            [
                'subject' => 'mathematics',
                'intent'  => 'math_percentage',
                'keywords' => ['percentage' => 3, 'percent' => 2, 'profit' => 1, 'loss' => 1, 'discount' => 1, 'interest' => 1],
                'response' => "📊 **Percentages**\n\nPercentage means **'per hundred'**.\n\n**Formula:** Percentage = (Part / Whole) × 100\n\n**Examples:**\n• 15% of 200 = (15/100) × 200 = **30**\n• If 40 out of 50 = (40/50) × 100 = **80%**\n\n**Profit & Loss:**\n• Profit % = (Profit / Cost Price) × 100\n• Loss % = (Loss / Cost Price) × 100\n• SP = CP × (100 + Profit%) ÷ 100\n\n💡 *Tip: To find x% of a number, multiply by x and divide by 100.*",
            ],
            [
                'subject' => 'mathematics',
                'intent'  => 'math_algebra',
                'keywords' => ['algebra' => 3, 'equation' => 2, 'variable' => 2, 'linear equation' => 2, 'solve' => 1, 'expression' => 1, 'linear' => 1],
                'response' => "🔣 **Algebra**\n\nAlgebra uses letters (variables) to represent unknown numbers.\n\n**Linear Equation:** ax + b = c\n*Solve: 2x + 3 = 11*\n→ 2x = 11 - 3 = 8\n→ x = 8 ÷ 2 = **4**\n\n**Key Rules:**\n• What you do to one side, do to the other\n• Collect like terms before solving\n• Transposing: changing side = changing sign\n\n**Algebraic Identities:**\n• (a+b)² = a² + 2ab + b²\n• (a-b)² = a² - 2ab + b²\n• (a+b)(a-b) = a² - b²",
            ],
            [
                'subject' => 'mathematics',
                'intent'  => 'math_geometry',
                'keywords' => ['area' => 2, 'perimeter' => 2, 'circle' => 2, 'triangle' => 1, 'rectangle' => 1, 'geometry' => 3, 'volume' => 2, 'surface area' => 2, 'square' => 1],
                'response' => "📐 **Geometry — Areas & Volumes**\n\n**2D Shapes:**\n• Rectangle: Area = l×b | Perimeter = 2(l+b)\n• Square: Area = a² | Perimeter = 4a\n• Triangle: Area = ½×base×height\n• Circle: Area = πr² | Circumference = 2πr\n\n**3D Shapes:**\n• Cube: Volume = a³ | SA = 6a²\n• Cuboid: Volume = l×b×h | SA = 2(lb+bh+lh)\n• Cylinder: Volume = πr²h | CSA = 2πrh\n\n💡 *Always include units in your answer (cm², m³, etc.)*",
            ],
            [
                'subject' => 'mathematics',
                'intent'  => 'math_number_system',
                'keywords' => ['natural number' => 2, 'whole number' => 2, 'integer' => 2, 'rational number' => 2, 'irrational' => 2, 'real number' => 2, 'number system' => 3, 'prime number' => 2, 'composite' => 1],
                'response' => "🔢 **Number System**\n\n**Types of Numbers:**\n• **Natural Numbers (N):** 1, 2, 3, 4... (counting numbers)\n• **Whole Numbers (W):** 0, 1, 2, 3... (natural + zero)\n• **Integers (Z):** ...-2, -1, 0, 1, 2... (positive + negative + zero)\n• **Rational Numbers (Q):** Numbers in form p/q (e.g., ½, 0.75)\n• **Irrational Numbers:** Cannot be expressed as p/q (e.g., √2, π)\n• **Real Numbers (R):** All rational + irrational numbers\n\n**Prime Numbers:** Divisible only by 1 and themselves (2, 3, 5, 7, 11...)\n**Composite Numbers:** Have more than 2 factors (4, 6, 8, 9...)\n\n💡 *1 is neither prime nor composite. 2 is the only even prime!*",
            ],

            // ═══════════════════════════ SCIENCE ═══════════════════════════════

            [
                'subject' => 'science',
                'intent'  => 'science_newton_laws',
                'keywords' => ['newton' => 3, 'law of motion' => 3, 'laws of motion' => 3, 'newtons law' => 3, 'inertia' => 2, 'force' => 2, 'momentum' => 2, 'action reaction' => 2, 'f=ma' => 2, 'fma' => 2, 'second law' => 2, 'third law' => 2, 'first law' => 2],
                'response' => "⚡ **Newton's Three Laws of Motion**\n\n**1st Law — Law of Inertia:**\nAn object at rest stays at rest, and an object in motion continues in motion, unless acted upon by an external force.\n💡 *Seat belts protect you because of inertia!*\n\n**2nd Law — F = ma:**\nForce = Mass × Acceleration\n*Example: Mass = 5 kg, Acceleration = 3 m/s² → Force = 15 N*\n\n**3rd Law — Action & Reaction:**\nFor every action, there is an equal and opposite reaction.\n💡 *A rocket moves upward because exhaust gases push downward!*\n\n**Unit of Force:** Newton (N)\n1 N = force that gives 1 kg an acceleration of 1 m/s²",
            ],
            [
                'subject' => 'science',
                'intent'  => 'science_photosynthesis',
                'keywords' => ['photosynthesis' => 3, 'chlorophyll' => 2, 'sunlight' => 1, 'carbon dioxide' => 2, 'co2' => 1, 'food plant' => 1, 'plant food' => 1, 'glucose' => 1, 'plants make food' => 2, 'how plants make' => 2],
                'response' => "🌿 **Photosynthesis**\n\nThe process by which green plants make their own food using sunlight.\n\n**Equation:**\n6CO₂ + 6H₂O + Light Energy → C₆H₁₂O₆ + 6O₂\n*(Carbon dioxide + Water + Sunlight → Glucose + Oxygen)*\n\n**What is needed:**\n• Chlorophyll (green pigment in leaves)\n• Sunlight (energy source)\n• Carbon dioxide (enters through stomata)\n• Water (absorbed by roots)\n\n**What is produced:**\n• Glucose (food stored as starch)\n• Oxygen (released — the air we breathe!)\n\n**Location:** Chloroplasts inside leaf cells\n\n💡 *Plants are called 'Producers' in a food chain because they make their own food!*",
            ],
            [
                'subject' => 'science',
                'intent'  => 'science_cell',
                'keywords' => ['cell' => 3, 'nucleus' => 2, 'mitochondria' => 2, 'membrane' => 1, 'organelle' => 2, 'plant cell' => 2, 'animal cell' => 2, 'unit of life' => 2],
                'response' => "🔬 **The Cell — Basic Unit of Life**\n\n**Cell Theory:** All living organisms are made of cells.\n\n**Key Organelles:**\n• **Nucleus** — Control center, contains DNA/chromosomes\n• **Mitochondria** — 'Powerhouse of the cell', produces energy (ATP)\n• **Cell Membrane** — Controls what enters and exits the cell\n• **Cytoplasm** — Jelly-like fluid filling the cell\n• **Ribosomes** — Protein synthesis\n\n**Plant Cell (extra parts):**\n• **Cell Wall** — Rigid outer layer (cellulose)\n• **Chloroplasts** — Site of photosynthesis\n• **Large Vacuole** — Stores water, maintains shape\n\n💡 *Smallest cell: Mycoplasma | Largest: Ostrich egg*",
            ],
            [
                'subject' => 'science',
                'intent'  => 'science_periodic_table',
                'keywords' => ['periodic table' => 3, 'element' => 2, 'atomic number' => 2, 'valency' => 2, 'metal' => 1, 'non-metal' => 1, 'nonmetal' => 1, 'noble gas' => 2, 'alkali' => 1],
                'response' => "⚗️ **Periodic Table**\n\nElements arranged by increasing **Atomic Number**.\n\n**Structure:**\n• 18 Groups (vertical columns) | 7 Periods (horizontal rows)\n• 118 elements total\n\n**Important Groups:**\n• Group 1 — Alkali Metals (Li, Na, K): very reactive\n• Group 17 — Halogens (F, Cl, Br): reactive non-metals\n• Group 18 — Noble Gases (He, Ne, Ar): inert/stable\n\n**First 10 Elements:**\nH, He, Li, Be, B, C, N, O, F, Ne\n*(Trick: 'HHeLiBeBCNOFNe')*\n\n💡 *Elements in the same group have similar chemical properties!*",
            ],
            [
                'subject' => 'science',
                'intent'  => 'science_light',
                'keywords' => ['light' => 2, 'reflection' => 3, 'refraction' => 3, 'lens' => 2, 'mirror' => 2, 'convex' => 2, 'concave' => 2, 'prism' => 2, 'spectrum' => 1, 'rainbow' => 1],
                'response' => "💡 **Light — Reflection & Refraction**\n\n**Reflection:** Light bouncing off a surface\n• Laws of Reflection: angle of incidence = angle of reflection\n• **Concave mirror**: converges light (used in headlights, telescopes)\n• **Convex mirror**: diverges light (used in rear-view mirrors — wider view)\n\n**Refraction:** Bending of light as it passes from one medium to another\n• Light slows down in denser medium → bends toward normal\n• **Convex lens**: converges light (magnifying glass, camera)\n• **Concave lens**: diverges light (spectacles for short-sightedness)\n\n**Dispersion:** White light splits into VIBGYOR through a prism\n*(Violet, Indigo, Blue, Green, Yellow, Orange, Red)*\n\n💡 *Rainbow is formed due to dispersion of sunlight through raindrops!*",
            ],

            // ═══════════════════════════ ENGLISH ════════════════════════════════

            [
                'subject' => 'english',
                'intent'  => 'english_tenses',
                'keywords' => ['tense' => 3, 'tenses' => 3, 'past tense' => 2, 'present tense' => 2, 'future tense' => 2, 'verb form' => 1, 'grammar' => 1],
                'response' => "📚 **English Tenses**\n\n**Present Tense:**\n• Simple: I eat / She eats\n• Continuous: I am eating\n• Perfect: I have eaten\n\n**Past Tense:**\n• Simple: I ate\n• Continuous: I was eating\n• Perfect: I had eaten\n\n**Future Tense:**\n• Simple: I will eat\n• Continuous: I will be eating\n• Perfect: I will have eaten\n\n💡 *Key helper verbs:*\n• Present → am/is/are\n• Past → was/were/had\n• Future → will/shall",
            ],
            [
                'subject' => 'english',
                'intent'  => 'english_parts_of_speech',
                'keywords' => ['parts of speech' => 3, 'noun' => 2, 'pronoun' => 2, 'adjective' => 2, 'adverb' => 2, 'conjunction' => 2, 'preposition' => 2, 'interjection' => 2],
                'response' => "📝 **Parts of Speech**\n\n1. **Noun** — Name of person/place/thing: *Nabha, book, happiness*\n2. **Pronoun** — Replaces a noun: *I, you, he, she, they, it*\n3. **Verb** — Action or state: *run, is, think, was*\n4. **Adjective** — Describes a noun: *tall, beautiful, three*\n5. **Adverb** — Modifies verb/adjective: *quickly, very, here*\n6. **Preposition** — Shows relationship: *in, on, at, under, between*\n7. **Conjunction** — Joins clauses: *and, but, because, although*\n8. **Interjection** — Expresses emotion: *Oh!, Wow!, Alas!*\n\n💡 *Every sentence needs at least a noun and a verb!*",
            ],

            // ═══════════════════════════ SOCIAL STUDIES ══════════════════════════

            [
                'subject' => 'social_studies',
                'intent'  => 'ss_punjab',
                'keywords' => ['punjab' => 3, 'nabha' => 2, 'chandigarh' => 2, 'ludhiana' => 1, 'amritsar' => 1, 'patiala' => 2, 'golden temple' => 1, 'capital of punjab' => 3],
                'response' => "🗺️ **Punjab — Key Facts**\n\n**Capital:** Chandigarh (shared with Haryana)\n**Language:** Punjabi | **Formation:** November 1, 1966\n\n**Important Cities:**\n• Amritsar — Golden Temple (Harmandir Sahib)\n• Ludhiana — Industrial capital\n• Patiala — Historical royal city\n• **Nabha** — Part of Patiala district, known for education\n\n**Rivers:** Sutlej, Beas, Ravi\n**Borders:** Himachal Pradesh, Haryana, Rajasthan, J&K, Pakistan\n\n🌾 **Agriculture:** Punjab is called the **'Granary of India'**\nMajor crops: Wheat, Rice, Sugarcane\nThe Green Revolution began here!\n\n🎊 **Famous for:** Bhangra, Giddha, Butter Chicken, Lassi",
            ],
            [
                'subject' => 'social_studies',
                'intent'  => 'ss_independence',
                'keywords' => ['independence' => 3, 'freedom' => 2, 'british' => 2, 'gandhi' => 2, '1947' => 3, 'partition' => 2, 'india independence' => 3, 'independence day' => 3],
                'response' => "🇮🇳 **Indian Independence (1947)**\n\n**Date:** August 15, 1947\n**First Prime Minister:** Pt. Jawaharlal Nehru\n**National Anthem:** Jana Gana Mana (by Rabindranath Tagore)\n\n**Key Freedom Fighters:**\n• Mahatma Gandhi — Father of the Nation, led Non-violence movement\n• Bhagat Singh — Revolutionary from Punjab\n• Subhas Chandra Bose — Netaji, INA\n• Sardar Patel — Iron Man of India\n• B.R. Ambedkar — Father of Indian Constitution\n\n**Important Events:**\n• 1857 — First War of Independence\n• 1919 — Jallianwala Bagh Massacre (Amritsar)\n• 1930 — Dandi March / Salt Satyagraha\n• 1942 — Quit India Movement\n• 1947 — Independence & Partition\n\n💡 *Republic Day = January 26, 1950 (Constitution adopted)*",
            ],
            [
                'subject' => 'social_studies',
                'intent'  => 'ss_constitution',
                'keywords' => ['constitution' => 3, 'fundamental rights' => 2, 'directive principles' => 2, 'parliament' => 2, 'president' => 1, 'preamble' => 2, 'lok sabha' => 2, 'rajya sabha' => 2],
                'response' => "⚖️ **Indian Constitution**\n\n**Adopted:** January 26, 1950\n**Father:** Dr. B.R. Ambedkar (Chairman, Drafting Committee)\n**Preamble:** India is a **Sovereign, Socialist, Secular, Democratic Republic**\n\n**6 Fundamental Rights:**\n1. Right to Equality (Art. 14-18)\n2. Right to Freedom (Art. 19-22)\n3. Right against Exploitation (Art. 23-24)\n4. Right to Freedom of Religion (Art. 25-28)\n5. Cultural & Educational Rights (Art. 29-30)\n6. Right to Constitutional Remedies (Art. 32) — the 'Heart of the Constitution'\n\n**Parliament:** Lok Sabha (Lower House) + Rajya Sabha (Upper House)\n\n💡 *The Indian Constitution is the longest written constitution in the world!*",
            ],

            // ═══════════════════════════ HINDI ═══════════════════════════════════

            [
                'subject' => 'hindi',
                'intent'  => 'hindi_swar_vyanjan',
                'keywords' => ['swar' => 3, 'vyanjan' => 3, 'vowel' => 1, 'consonant' => 1, 'varna mala' => 3, 'hindi alphabet' => 2, 'devanagari' => 2, 'hindi grammar' => 2],
                'response' => "हिंदी वर्णमाला 🔤\n\n**स्वर (Vowels) — 11:**\nअ आ इ ई उ ऊ ऋ ए ऐ ओ औ\n\n**व्यंजन (Consonants) — 33:**\nक ख ग घ ङ (क-वर्ग)\nच छ ज झ ञ (च-वर्ग)\nट ठ ड ढ ण (ट-वर्ग)\nत थ द ध न (त-वर्ग)\nप फ ब भ म (प-वर्ग)\n\nय र ल व (अर्धस्वर)\nश ष स ह (उष्म व्यंजन)\n\n💡 *हिंदी भारत की राजभाषा है। इसे देवनागरी लिपि में लिखा जाता है।*",
            ],
        ];
    }
}
