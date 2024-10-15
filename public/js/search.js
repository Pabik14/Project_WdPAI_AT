async function fetchAnime(data) {
    const response = await fetch("/searchAnime", {
        method: "POST",
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    if (!response.ok) {
        throw new Error('Błąd sieciowy podczas pobierania danych.');
    }

    return await response.json();
}

// Obsługa zmiany statusu
document.querySelector('#status-filter').addEventListener('change', async function() {
    updateAnimeList();
});

// Nasłuchuj wpisywania w pole wyszukiwania
document.querySelector('#search-input').addEventListener('keyup', async function(e) {
    updateAnimeList();
});

// Przycisk resetu
document.querySelector('#reset-search').addEventListener('click', async function() {
    document.querySelector('#search-input').value = ''; // Wyczyść pole wyszukiwania
    document.querySelector('#status-filter').value = ''; // Wyczyść wybór statusu
    updateAnimeList();
});

// Funkcja do aktualizacji listy anime na podstawie wyszukiwania i statusu
async function updateAnimeList() {
    const query = document.querySelector('#search-input').value.trim();
    const status = document.querySelector('#status-filter').value;

    try {
        // Przygotuj dane do wysłania
        const data = { search: query, status: status };

        // Wykonaj zapytanie
        const animeList = await fetchAnime(data);

        // Wywołanie funkcji renderującej
        renderAnimeList(animeList);
    } catch (error) {
        console.error('Błąd podczas wyszukiwania:', error);
    }
}

function renderAnimeList(animeList) {
    const animeListContainer = document.querySelector('.anime-table tbody');

    // Wyczyść poprzednią listę
    animeListContainer.innerHTML = '';

    if (animeList.length > 0) {
        // Iteruj przez każdy element listy i stwórz wiersze w tabeli
        animeList.forEach(anime => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${anime.anime_name}</td>
                <td>${anime.category}</td>
                <td>${anime.type}</td>
                <td>${anime.status}</td>
                <td>${anime.episodes_count}</td>
                <td>
                    <img 
                        src="public/img/x.png" 
                        alt="Delete" 
                        class="delete-anime x-delete" 
                        data-anime-id="${anime.id}" 
                    >
                </td>
            `;
            animeListContainer.appendChild(row);
        });
    } else {
        // Jeśli brak wyników
        animeListContainer.innerHTML = '<tr><td colspan="6">No results!</td></tr>';
    }
}

// Nasłuchuj kliknięć na ikony usuwania
document.querySelector('#anime-table-body').addEventListener('click', async function(event) {
    if (event.target.classList.contains('delete-anime')) {
        const animeId = event.target.dataset.animeId; // Pobierz `animeId` z atrybutu `data-anime-id`

        console.log('Clicked delete for anime ID:', animeId); // Debugging

        if (animeId) {
            try {
                // Wyślij żądanie DELETE do serwera
                const response = await fetch('/deleteAnime', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ anime_id: animeId })
                });

                const result = await response.json();

                if (result.success) {
                    // Usuń wiersz z tabeli w interfejsie użytkownika
                    event.target.closest('tr').remove();
                } else {
                    console.error('Błąd podczas usuwania:', result.error);
                }
            } catch (error) {
                console.error('Błąd podczas usuwania:', error);
            }
        }
    }
});
