document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    searchInput.addEventListener('input', function() {
        searchResults.innerHTML = '';

        const query = this.value;
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }
        
        fetch('/search/users?query=' + query)
            .then(response => response.json())
            .then(users => {
                let resultsHTML = '';
                users.forEach(user => {
                    resultsHTML += `<a href="/users/${user.id}" class="search-result-item">${user.username} - ${user.email} \n</a>`;
                });
                searchResults.innerHTML = resultsHTML;
                searchResults.style.display = 'block';
            })
            .catch(error => {
                console.error(error);
            });
    });


    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target)) {
            searchResults.style.display = 'none';
        }
    });
});
