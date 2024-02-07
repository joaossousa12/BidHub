function handleNotificationsDropdown() {
    let dropdownButton = document.querySelector("#notificationsDropdown");

    if (dropdownButton) {
        dropdownButton.addEventListener("click", function () {
            // Toggle the dropdown menu visibility
            let dropdownMenu = document.querySelector(".dropdown-menu");
            dropdownMenu.classList.toggle("show");

            // Mark notifications as viewed when checkbox is checked
            let checkboxes = document.querySelectorAll(".notification-checkbox");
            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener("change", function () {
                    // Reload the page or perform other actions when the checkbox is changed
                    // You can add an AJAX call here to update the "viewed" status in the database
                    location.reload();
                });
            });
        });
    }
}

document.addEventListener("DOMContentLoaded", handleNotificationsDropdown);
// notification.js

document.addEventListener("DOMContentLoaded", function () {
    handleNotifications();
});

function handleNotifications() {
    let checkboxes = document.querySelectorAll('.notification-checkbox');

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            let notificationId = this.getAttribute('data-notification-id');
            let viewed = this.checked;

            // Send an AJAX request to update the viewed status
            updateNotificationStatus(notificationId, viewed);
        });
    });
}

function updateNotificationStatus(notificationId, viewed) {
    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Send an AJAX request to update the viewed status
    $.ajax({
        url: '/update-notification-status',
        type: 'POST',
        data: {
            _token: token,
            notificationId: notificationId,
            viewed: viewed
        },
        success: function (response) {
            if (response.success) {
                console.log('Notification status updated successfully.');
            } else {
                console.error('Failed to update notification status.');
            }
        },
        error: function (error) {
            console.error('Error updating notification status:', error);
        }
    });
}

document.addEventListener("DOMContentLoaded", updateNotificationStatus);
// notification.js