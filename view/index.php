<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Películas IMDb</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .toast.show {
            opacity: 1;
        }
        .toast.error {
            background: #f44336;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center mb-8 text-indigo-800">Películas Populares</h1>
        <div id="peliculas" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <!-- Películas se cargarán aquí -->
        </div>

        <h2 class="text-3xl font-bold text-center mt-16 mb-8 text-indigo-800">Mis Favoritos</h2>
        <div id="favoritos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <!-- Favoritos se cargarán aquí -->
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script>
        let favoritosSet = new Set();

        function showToast(message, isError = false) {
            console.log('Toast:', message);
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.toggle('error', isError);
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        async function loadPeliculas() {
            try {
                const response = await fetch('/peliculas/api.php?action=peliculas');
                const peliculas = await response.json();
                const container = document.getElementById('peliculas');
                console.log('Cargando películas, set actual:', Array.from(favoritosSet));
                container.innerHTML = peliculas.map(p => {
                    const isFav = favoritosSet.has(p.imdb_id);
                    console.log('Película:', p.imdb_id, 'es favorito:', isFav);
                    return `
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                        <img src="${p.poster || 'https://via.placeholder.com/300x450?text=No+Image'}" alt="${p.nombre}" class="w-full h-80 object-contain bg-gray-200">
                        <div class="p-6">
                            <h2 class="text-xl font-bold mb-2 text-gray-800">${p.nombre}</h2>
                            <p class="text-gray-600 mb-2">${p.anio || ''} - ${p.genero || 'Sin género'}</p>
                            <p class="text-sm text-gray-700 mb-4">${p.descripcion ? p.descripcion.substring(0, 100) + '...' : 'Sin descripción'}</p>
                            ${isFav ?
                                `<button onclick="removeFavorito('${p.imdb_id}')" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-200">
                                    Eliminar de Favoritos
                                </button>` :
                                `<button data-pelicula='${btoa(JSON.stringify(p))}' onclick="addToFavorites(this)" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-200">
                                    Agregar a Favoritos
                                </button>`
                            }
                        </div>
                    </div>
                `}).join('');
            } catch (error) {
                showToast('Error al cargar películas', true);
            }
        }

        async function loadFavoritos() {
            try {
                const response = await fetch('/peliculas/api.php?action=favoritos');
                const favoritos = await response.json();
                console.log('Favoritos cargados:', favoritos);
                favoritosSet = new Set(favoritos.map(f => f.imdb_id));
                console.log('Set actualizado:', Array.from(favoritosSet));
                const container = document.getElementById('favoritos');
                container.innerHTML = favoritos.map(f => `
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                        <img src="${f.poster || 'https://via.placeholder.com/300x450?text=No+Image'}" alt="${f.nombre}" class="w-full h-80 object-contain bg-gray-200">
                        <div class="p-6">
                            <h2 class="text-xl font-bold mb-2 text-gray-800">${f.nombre}</h2>
                            <p class="text-gray-600 mb-2">${f.anio || ''} - ${f.genero || 'Sin género'}</p>
                            <p class="text-sm text-gray-700 mb-4">${f.descripcion ? f.descripcion.substring(0, 100) + '...' : 'Sin descripción'}</p>
                            <button onclick="removeFavorito('${f.imdb_id}')" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-200">
                                Eliminar de Favoritos
                            </button>
                        </div>
                    </div>
                `).join('');
                loadPeliculas(); // Recargar películas para actualizar botones
            } catch (error) {
                showToast('Error al cargar favoritos', true);
            }
        }

        async function toggleFavorito(imdb_id, nombre, genero, anio, descripcion, poster) {
            if (anio === 'null') anio = null;
            try {
                const response = await fetch('/peliculas/api.php?action=favoritos', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'add', imdb_id, nombre, genero, anio, descripcion, poster })
                });
                const result = await response.json();
                console.log('Resultado de agregar:', result);
                if (result.success) {
                    showToast('Película agregada a favoritos');
                    loadFavoritos();
                } else {
                    showToast('Error al agregar a favoritos: ' + (result.error || JSON.stringify(result)), true);
                }
            } catch (error) {
                showToast('Error al agregar a favoritos', true);
            }
        }

        function addToFavorites(button) {
            console.log('data-pelicula:', button.dataset.pelicula);
            const decoded = atob(button.dataset.pelicula);
            console.log('decoded:', decoded);
            const p = JSON.parse(decoded);
            console.log('Agregando película:', p.imdb_id);
            toggleFavorito(p.imdb_id, p.nombre, p.genero, p.anio, p.descripcion, p.poster);
        }

        async function removeFromFavorites(imdb_id) {
            try {
                const response = await fetch('/peliculas/api.php?action=favoritos', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'remove', imdb_id })
                });
                const result = await response.json();
                if (result.success) {
                    showToast('Película eliminada de favoritos');
                    loadFavoritos();
                } else {
                    showToast('Error al eliminar de favoritos: ' + (result.error || ''), true);
                }
            } catch (error) {
                showToast('Error al eliminar de favoritos', true);
            }
        }

        async function removeFavorito(imdb_id) {
            try {
                const response = await fetch('/peliculas/api.php?action=favoritos', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete', imdb_id })
                });
                const result = await response.json();
                if (result.success) {
                    showToast('Película eliminada de favoritos');
                    loadFavoritos();
                } else {
                    showToast('Error al eliminar de favoritos: ' + (result.error || JSON.stringify(result)), true);
                }
            } catch (error) {
                showToast('Error al eliminar de favoritos', true);
            }
        }

        window.onload = () => {
            loadFavoritos();
        };
    </script>
</body>
</html>