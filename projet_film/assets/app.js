import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// Carousel navigation (home)
const carousel = document.querySelector('.carousel');
const prevBtn = document.querySelector('.carousel-prev');
const nextBtn = document.querySelector('.carousel-next');

if (carousel && prevBtn && nextBtn) {
    nextBtn.addEventListener('click', () => {
        carousel.scrollBy({ left: 320, behavior: 'smooth' });
    });

    prevBtn.addEventListener('click', () => {
        carousel.scrollBy({ left: -320, behavior: 'smooth' });
    });
}

document.addEventListener("DOMContentLoaded", () => {
    // Simple register password confirmation
    const form = document.getElementById("registerForm");
    if (form) {
        form.addEventListener("submit", (e) => {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirmPassword").value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert("Les mots de passe ne correspondent pas !");
            }
        });
    }

    // Auto-hide toast notices on profile
    document.querySelectorAll('.toast-notice').forEach((toast) => {
        setTimeout(() => {
            toast.remove();
        }, 4000);
    });

    // Live advanced search on films index
    const searchInput = document.getElementById('search-term');
    const suggestionsBox = document.getElementById('search-suggestions');
    const liveResults = document.getElementById('live-search-results');
    const liveGrid = document.getElementById('live-results-grid');
    const genreSelect = document.getElementById('genre');
    const anneeInput = document.getElementById('annee');

    let debounceTimer;

    const renderResults = (items) => {
        if (!liveResults || !liveGrid) return;
        liveGrid.innerHTML = '';
        if (!items || items.length === 0) {
            liveResults.classList.add('d-none');
            return;
        }
        items.forEach((item) => {
            const card = document.createElement('div');
            card.className = 'col';
            card.innerHTML = `
                <div class="card h-100">
                    <img src="${item.affiche || '/images/banniere.jpg'}" class="card-img-top" alt="Affiche de ${item.titre}">
                    <div class="card-body">
                        <h5 class="card-title">${item.titre}</h5>
                        <p class="card-text text-muted">${item.annee ?? ''}</p>
                        <p class="card-text fw-bold">${item.prix ?? ''} €</p>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <a class="btn btn-sm btn-outline-primary" href="/film/${item.id}">Détails</a>
                    </div>
                </div>`;
            liveGrid.appendChild(card);
        });
        liveResults.classList.remove('d-none');
    };

    const renderSuggestions = (suggestions) => {
        if (!suggestionsBox) return;
        suggestionsBox.innerHTML = '';
        if (!suggestions || suggestions.length === 0) {
            suggestionsBox.classList.add('d-none');
            return;
        }
        suggestions.forEach((s) => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action';
            item.textContent = s;
            item.addEventListener('click', () => {
                searchInput.value = s;
                suggestionsBox.classList.add('d-none');
                triggerSearch();
            });
            suggestionsBox.appendChild(item);
        });
        suggestionsBox.classList.remove('d-none');
    };

    const triggerSearch = () => {
        const term = (searchInput?.value || '').trim();
        const hasTerm = term.length > 0;
        const hasFilters = !!(genreSelect?.value || anneeInput?.value);

        if (!hasTerm && !hasFilters) {
            renderResults([]);
            renderSuggestions([]);
            return;
        }

        const params = new URLSearchParams();
        if (hasTerm) params.set('q', term);
        if (genreSelect?.value) params.set('genres', genreSelect.value);
        if (anneeInput?.value) {
            params.set('annee_min', anneeInput.value);
            params.set('annee_max', anneeInput.value);
        }

        fetch(`/films/search?${params.toString()}`)
            .then((r) => r.json())
            .then((data) => {
                renderResults(data.results || []);
                // On n'affiche des suggestions que si un terme est présent
                renderSuggestions(hasTerm ? data.suggestions || [] : []);
            })
            .catch(() => {
                renderResults([]);
                renderSuggestions([]);
            });
    };

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(triggerSearch, 250);
        });
        searchInput.addEventListener('focus', triggerSearch);
    }

    if (genreSelect) {
        genreSelect.addEventListener('change', triggerSearch);
    }

    if (anneeInput) {
        anneeInput.addEventListener('change', triggerSearch);
        anneeInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(triggerSearch, 250);
        });
    }
});
