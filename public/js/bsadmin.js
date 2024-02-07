function handleEditAuctionButton() {
    let editButton = document.querySelector("#edit-auction");
    if (editButton) {
        editButton.addEventListener("click", function() {
            let auctionId = getAuctionID();
            window.location.href = `/auction/${auctionId}/edit`;
        });
    }
}

function getAuctionID() {
    let path = window.location.pathname.split('/');
    return path[path.indexOf('auction') + 1];
}

document.addEventListener("DOMContentLoaded", handleEditAuctionButton);

function handleEditUserButton() {
    let editButton = document.querySelector("#edit-user");
    if (editButton) {
        editButton.addEventListener("click", function() {
            let userId = getUserId();
            window.location.href = `/users/${userId}/edit`;
        });
    }
}

function getUserId() {
    let editButton = document.getElementById('edit-user');
    if (editButton) {
        let classList = editButton.className.split(/\s+/);
        let userIdClass = classList.find(cls => cls.startsWith('user'));
        if (userIdClass) {
            return userIdClass.substring(4);
        }
    }
    return null;
}

function initializeCountdown(dateCreated, duration, auction_id) {
    var durationInMilliseconds = duration * 86400000;

    var endTime = new Date(dateCreated).getTime() + durationInMilliseconds;

    var countdownFunction = setInterval(function() {
        var now = new Date().getTime();
        var distance = endTime - now;

        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        var timeLeftElement = document.getElementById("timeLeft");
        if (timeLeftElement) {
            timeLeftElement.innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
        }

        if (timeLeftElement) {
            if(days == 0 && hours == 0 && minutes == 5 && seconds == 0){
                callTimeEndingFunction(auction_id);
            }
        }

        if (distance < 0) {
            clearInterval(countdownFunction);
            timeLeftElement.innerHTML = "Auction has ended!";
            console.log("going to end");
            callTimeEndFunction(auction_id);
            console.log("going to winner");
            callWinnerAnnounce(auction_id);
            if (timeLeftElement) {
                timeLeftElement.innerHTML = "Auction has ended!";
            }
        }
    }, 1000);
}

function callTimeEndFunction(auction_id) {
    fetch('/auction/time-end/' + auction_id)
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log(data);
    })
    .catch(error => {
        console.error('Fetch error:', error);
    });
}

function callWinnerAnnounce(auction_id) {
    fetch('/auction/winner/' + auction_id)
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log(data);
    })
    .catch(error => {
        console.error('Fetch error:', error);
    });
}

function callTimeEndingFunction(auction_id) {
    fetch('/auction/time-ending/' + auction_id)
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log(data);
    })
    .catch(error => {
        console.error('Fetch error:', error);
    });
}

document.addEventListener("DOMContentLoaded", function() {
    handleEditUserButton();
});

function handleBidButton() {
    let bidBox = document.querySelector("#bid-box");

    if (bidBox) {
        bidBox.addEventListener("click", function() {
            let currentBidValue = document.querySelector("#currentBid").value;
            let auctionID = getAuctionID();
            let currentMaxBid = parseFloat(document.querySelector("#currentMaxBid").innerText.replace('€', ''));

            if (!currentBidValue || parseFloat(currentBidValue) <= currentMaxBid) {
                alert("Por favor, insira um valor de lance válido e maior que o lance atual.");
                return;
            }

            let params = {
                auctionID: auctionID,
                value: parseFloat(currentBidValue)
            };

            ajaxCallPost('/api/bid', params, postBidHandler);
        });
    }
}

function ajaxCallPost(url, params, handler)
{
    let token = document.querySelector("#csrfToken").content;
    params._token = token;
    $.ajax(
    {
        url: url,
        type: 'POST',
        data: params,
        success: handler
    });
}

function postBidHandler(data) {
    if (data.success) {
        alert(data.message);
        if (data.timeExtended) {
            initializeCountdown(auction.datecreated, auction.duration, auction.id);
        }
    } else {
        alert("Erro ao enviar o lance: " + data.message);
    }
}

function getAuctionID() {
    let path = window.location.pathname.split('/');
    return path[path.indexOf('auction') + 1];
}

document.addEventListener("DOMContentLoaded", handleBidButton);

function moderatorAction(modAction, auctionId, auctionModId = -1)
{
    $.ajaxSetup(
    {
        headers:
        {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax(
    {
        url: "/api/moderator",
        method: 'post',
        data:
        {
            ida: auctionId,
            idm: auctionModId,
            action: modAction
        },

        success: function(result)
        {
            // Fade elements on approve/remove
            if (window.location.pathname === "/moderator")
            {
                if (modAction == "approve_creation" || modAction == "remove_creation")
                {
                    $(`#cr-${auctionId}`).fadeOut();
                }
                else if (modAction == "get_new_description")
                {
                    let description = JSON.parse(result);
                    let action_approve = "moderatorAction('approve_modification'," + auctionId + "," + auctionModId + ")";
                    let action_remove = "moderatorAction('remove_modification'," + auctionId + "," + auctionModId + ")";
                    //put description text in modal
                    $("#bookTitle").text(description.title);
                    $("#oldDescription").text(description.old);
                    $("#newDescription").text(description.new);
                    //change action of modal buttons
                    $("#approveBtn").attr("onclick", action_approve);
                    $("#removeBtn").attr("onclick", action_remove);

                }
                else
                {
                    $(`#mr-${auctionId}`).fadeOut();
                }
            }
            else
            {
                location.reload();
            }
        },
        error: function(data)
        {
            console.log(data);
            alert("Check the log.")
        }
    });
}

function deleteAuction(id) {
    if (confirm('Are you sure you want to delete this auction?')) {
        // Send AJAX request to the server to delete the auction
        $.ajax({
            url: '/auction/' + id + '/delete',
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                _method: 'DELETE'
            },
            success: function(result) {
                // If successful, handle the result (e.g., show a message, remove the auction from the view, etc.)
                alert('Auction deleted successfully');
                // Optionally, remove the auction element from the DOM or redirect as needed
                window.location.href = '/'; // Redirect to the home page or another appropriate page
            },
            error: function(xhr, status, error) {
                // Handle any errors
                alert('Error deleting auction');
            }
        });
    }
}
