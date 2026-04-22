<?php

namespace Database\Seeders;

use App\Models\ChatbotQA;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChatbotQaSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $by    = $admin?->id;

        $entries = [
            [
                'question' => 'what is newtons law',
                'keywords' => 'newton, laws of motion, inertia, force, f=ma, action reaction, first law, second law, third law',
                'answer'   => "⚡ **Newton's Three Laws of Motion**\n\n**1st Law (Inertia):** An object at rest stays at rest, and an object in motion stays in motion unless acted upon by an external force.\n💡 *Seat belts work because of inertia!*\n\n**2nd Law (F = ma):** Force = Mass × Acceleration\n*Example: 5 kg × 3 m/s² = 15 N*\n\n**3rd Law (Action-Reaction):** For every action there is an equal and opposite reaction.\n💡 *Rockets move because exhaust gases push downward!*\n\n**Unit of Force:** Newton (N)",
            ],
            [
                'question' => 'what is photosynthesis',
                'keywords' => 'photosynthesis, chlorophyll, sunlight, carbon dioxide, glucose, oxygen, plant food, how plants make food',
                'answer'   => "🌿 **Photosynthesis**\n\nThe process by which green plants make their own food using sunlight.\n\n**Equation:**\n6CO₂ + 6H₂O + Light → C₆H₁₂O₆ + 6O₂\n\n**What is needed:**\n• Chlorophyll (green pigment)\n• Sunlight\n• Carbon dioxide (via stomata)\n• Water (via roots)\n\n**Products:** Glucose (food) + Oxygen (released)\n\n💡 *Plants are called Producers because they make their own food!*",
            ],
            [
                'question' => 'pythagoras theorem',
                'keywords' => 'pythagoras, pythagorean, hypotenuse, right triangle, right angle triangle, a squared b squared',
                'answer'   => "📐 **Pythagoras Theorem**\n\nIn a right-angled triangle:\n**a² + b² = c²**\n\n• **c** = hypotenuse (longest side)\n• **a, b** = the other two sides\n\n**Example:** a=3, b=4 → c = √(9+16) = **5**\n\n📌 3-4-5 is the most famous Pythagorean Triple!",
            ],
            [
                'question' => 'what is fraction',
                'keywords' => 'fraction, fractions, numerator, denominator, proper fraction, improper fraction, mixed number',
                'answer'   => "🔢 **Fractions**\n\nA fraction represents a **part of a whole**: Numerator / Denominator\n\n**Types:**\n• Proper: 3/4 (numerator < denominator)\n• Improper: 5/3 (numerator > denominator)\n• Mixed: 1½\n\n**Operations:**\n• Add/Subtract → equal denominators first\n• Multiply → numerator × numerator, denominator × denominator\n• Divide → multiply by the reciprocal\n\n💡 *Simplify by dividing both by their HCF*",
            ],
            [
                'question' => 'what is hcf and lcm',
                'keywords' => 'hcf, lcm, highest common factor, lowest common multiple, gcd, common factor',
                'answer'   => "🔢 **HCF and LCM**\n\n**HCF** = largest number dividing two numbers exactly\n*HCF(12, 18) = 6*\n\n**LCM** = smallest number divisible by both\n*LCM(4, 6) = 12*\n\n**Key formula:** HCF × LCM = Product of the two numbers\n\n**Prime factorization:**\n12 = 2² × 3 | 18 = 2 × 3²\nHCF = 6 | LCM = 36",
            ],
            [
                'question' => 'what is cell',
                'keywords' => 'cell, nucleus, mitochondria, organelle, plant cell, animal cell, unit of life, cytoplasm',
                'answer'   => "🔬 **The Cell — Basic Unit of Life**\n\n**Key Organelles:**\n• **Nucleus** — Control center, contains DNA\n• **Mitochondria** — 'Powerhouse', produces energy (ATP)\n• **Cell Membrane** — Controls entry/exit of substances\n• **Cytoplasm** — Jelly-like fluid\n• **Ribosomes** — Make proteins\n\n**Plant cell (extra):**\n• Cell Wall (cellulose)\n• Chloroplasts (photosynthesis)\n• Large central vacuole\n\n💡 *Smallest cell: Mycoplasma | Largest: Ostrich egg*",
            ],
            [
                'question' => 'what is algebra',
                'keywords' => 'algebra, equation, variable, linear equation, solve, expression',
                'answer'   => "🔣 **Algebra**\n\nUses letters (variables) to represent unknown numbers.\n\n**Solve:** 2x + 3 = 11\n→ 2x = 8 → x = **4**\n\n**Identities:**\n• (a+b)² = a² + 2ab + b²\n• (a-b)² = a² - 2ab + b²\n• (a+b)(a-b) = a² - b²\n\n**Rule:** Whatever you do to one side, do to the other.",
            ],
            [
                'question' => 'what is photosynthesis equation',
                'keywords' => 'photosynthesis equation, co2 water glucose oxygen, 6co2',
                'answer'   => "🌿 **Photosynthesis Equation**\n\n**6CO₂ + 6H₂O + Light Energy → C₆H₁₂O₆ + 6O₂**\n\nCarbon dioxide + Water + Sunlight → Glucose + Oxygen\n\nThis happens inside **chloroplasts** in leaf cells.",
            ],
            [
                'question' => 'capital of punjab',
                'keywords' => 'punjab, chandigarh, nabha, amritsar, patiala, ludhiana, capital',
                'answer'   => "🗺️ **Punjab**\n\n**Capital:** Chandigarh (shared with Haryana)\n**Language:** Punjabi\n**Formation:** November 1, 1966\n\n**Key Cities:**\n• Amritsar — Golden Temple\n• Ludhiana — Industrial capital\n• Patiala — Historical royal city\n• Nabha — Part of Patiala district\n\n🌾 Punjab is called the **'Granary of India'** (wheat, rice)",
            ],
            [
                'question' => 'what is percentage',
                'keywords' => 'percentage, percent, profit, loss, discount, per hundred',
                'answer'   => "📊 **Percentages**\n\nMeans **'per hundred'**.\n\n**Formula:** Percentage = (Part ÷ Whole) × 100\n\n**Example:** 15% of 200 = (15 ÷ 100) × 200 = **30**\n\n**Profit & Loss:**\n• Profit% = (Profit ÷ Cost Price) × 100\n• SP = CP × (100 + Profit%) ÷ 100",
            ],
        ];

        foreach ($entries as $entry) {
            ChatbotQA::firstOrCreate(
                ['question' => $entry['question']],
                [...$entry, 'created_by' => $by]
            );
        }
    }
}
