async function fetchAnime(data) {
    const response = await fetch("/searchAnime", {
        method: "POST",
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    if (!response.ok) {
        throw new Error('Network error while downloading data.');
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

        const animeList = await fetchAnime(data);

        renderAnimeList(animeList);
    } catch (error) {
        console.error('Error when searching:', error);
    }
}

function renderAnimeList(animeList) {
    const animeListContainer = document.querySelector('.anime-table tbody');

    animeListContainer.innerHTML = '';

    if (animeList.length > 0) {
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
        animeListContainer.innerHTML = '<tr><td colspan="6">No results!</td></tr>';
    }
}

document.querySelector('#anime-table-body').addEventListener('click', async function(event) {
    if (event.target.classList.contains('delete-anime')) {
        const animeId = event.target.dataset.animeId; 

        console.log('Clicked delete for anime ID:', animeId); // Debugging

        if (animeId) {
            try {
                const response = await fetch('/deleteAnime', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ anime_id: animeId })
                });

                const result = await response.json();

                if (result.success) {
                    event.target.closest('tr').remove();
                } else {
                    console.error('Error when deleting:', result.error);
                }
            } catch (error) {
                console.error('Error when deleting:', error);
            }
        }
    }
});
