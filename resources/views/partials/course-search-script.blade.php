<script>
function courseSearch() {
    return {
        query: '',
        results: [],
        loading: false,
        open: false,
        _timer: null,

        search() {
            clearTimeout(this._timer);
            if (this.query.length < 3) {
                this.results = [];
                this.open    = false;
                return;
            }
            this.loading = true;
            this._timer  = setTimeout(async () => {
                try {
                    const res = await fetch(
                        '/search/courses?q=' + encodeURIComponent(this.query),
                        { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
                    );
                    this.results = await res.json();
                    this.open    = true;
                } catch {
                    this.results = [];
                    this.open    = false;
                } finally {
                    this.loading = false;
                }
            }, 300);
        },
    };
}
</script>
