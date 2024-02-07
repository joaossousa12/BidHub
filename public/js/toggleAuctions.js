document.getElementById('view-all-button').addEventListener('click', function() {
    var latestAuctions = document.getElementById('latest-auctions');
    var allAuctions = document.getElementById('all-auctions');

    if (allAuctions.style.display === 'none') {
        allAuctions.style.display = 'block';
        latestAuctions.style.display = 'none';
        this.textContent = 'View Less';
    } else {
        allAuctions.style.display = 'none';
        latestAuctions.style.display = 'block';
        this.textContent = 'View All';
    }
});
