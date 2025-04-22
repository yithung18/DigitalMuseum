<?php
session_start();
$database = new SQLite3('museum.db');

$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? 'Guest';
$mute_status = $_SESSION['mute'] ?? '0';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Museum</title>    
    <style>
		.comment-container {
			display: flex;
			align-items: flex-start;
			gap: 10px;
			margin-bottom: 10px;
		}
		.comment-avatar {
    width: 50px !important;
    height: 50px !important;
    border-radius: 50%;
    object-fit: cover;
}

		.comment-text {
    background: #f1f1f1;
    padding: 10px;
    border-radius: 5px;
    max-width: 80%;
    text-align: left; /* Ensures text starts from the left */
}

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: rgb(239, 239, 232);
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ada48e;
            padding: 5px;
            position: relative;
            z-index: 2;
        }

        .navbar .search-bar {
            display: flex;
            align-items: center;
            margin-left: 25px;
            width: 200px;
        }
        .search-bar input[type="text"] {
            flex-grow: 1;
            width: 80%;
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 20px;
        }
        .search-bar button {
            margin-left: -30px;
            padding: 5px 10px;
            font-size: 14px;
            border: none;
            color: #969292;
            background: none;
            cursor: pointer;
        }
        .navbar .museum-title {
            text-align: center;
            font-size: 17px;
            font-family: Georgia, 'Times New Roman', Times, serif;
            margin-right: 100px;
        }
        .menu-icon {
            font-size: 24px;
            cursor: pointer;
            margin-right: 20px;
        }

        .side-menu {
            position: fixed;
            top: 0;
            right: -250px;
            width: 250px;
            height: 100%;
            background-color: #c8bca4;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.3);
            transition: right 0.3s ease-in-out;
            padding-top: 20px;
            z-index: 100; /* Ensures it's above everything */
        }

        .side-menu a {
            display: block;
            padding: 10px 20px;
            font-size: 18px;
            text-decoration: none;
            color: black;
        }

        .side-menu a:hover {
            background-color: #b0a58a;
        }

        .side-menu .close-btn {
            font-size: 24px;
            cursor: pointer;
            padding: 10px;
            display: block;
            text-align: right;
        }

        .banner {
            position:relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 94%;
            max-width: 1200px;
            height: 200px;
            margin: 25px auto;
            overflow: hidden;
        }
        .banner-container {
            display: flex;
            width: 100%;
            height: 100vh;
            transition: transform 0.5s ease-in-out;
        }
        .banner-slide {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-width: 100%;
            height: 100vh;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            text-decoration: none;
        }
        .banner-slide .message {
            color: white;
            font-family:'Garamond';
            font-weight: bold;
            font-size: 24px;
            text-align: center;
        }

        .section {
            margin: 25px;
            margin-top: 50px;
            position: relative;
        }
        .section h3{
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-left: 20px;
        }
        .section .addItem-btn{
            border: none;
            cursor: pointer;
            font-size: 11px;
            color: rgb(97, 56, 231);
            text-decoration: underline;
            margin-bottom: 10px;
        }

        .card {
            width: 200px !important; /* Ensures a fixed width */
            min-width: 200px; /* Prevents shrinking */
            height: auto;
            border: none;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            flex-shrink: 0; /* Prevents flexbox from squeezing the cards */
            transition: transform 0.5s;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.3);
        }
        .card:hover{
            transform: scale(1.1);
        }
        .card h4{   /*for title*/
            font-family: 'Times New Roman', Times, serif;
            font-size: 20px;
            white-space: break-spaces;
            max-height: 50px;
            max-width: max-content;
            overflow:hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            display: block;
        }
        .card p{
            font-size: 14px;
            font-family:'Times New Roman', Times, serif;
            font-weight: lighter;
            white-space: break-spaces;
            max-height: 70px;
            overflow: hidden;   
            text-overflow:ellipsis;  /* Show "..." */
            max-width: 100%; /* Ensure it doesn't overflow */
            display: block;
        }

        .cards-container {
            display: flex;
            position: relative;
            overflow-x: auto;  /* Enable horizontal scrolling */
            white-space: nowrap;
            gap: 20px;
            padding: 25px;
            scroll-behavior: smooth;
        }
        .cards-container::-webkit-scrollbar{  /*hide scrollbar*/
            display: none;
        }

        .scroll-btn,
        .scrollBanner-btn,
        .scrollNew-btn {
            position: absolute;
            top: 45%;
            height: auto;
            width: 40px;
            background-color: rgba(0, 0, 0, 0.15);
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px;
            font-size: 18px;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.5s;
        }
        
        .scrollBanner-btn:hover,
        .scrollNew-btn:hover,
        .scroll-btn:hover {
            transform: scale(1.1);
            background-color: rgba(0, 0, 0, 0.25);
        }
        .scroll-left{
            left:0;
        }
        .scroll-right{
            right:0;
        }
        .scrollBanner-left{
            left:10px;
        }
        .scrollBanner-right{
            right:10px;
        }

        .edit-btn{
            font-size: 20px;
            cursor: pointer;
            margin-left: 10px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 4;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 350px;
            background: white;
            padding: 20px;
            border: 2px solid black;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .modal-content {
            text-align: center;
        }
        .details-modal-backdrop{
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            z-index: 200;
            pointer-events: auto;
        }
        .details-modal{
            display: none; /* Hidden by default */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            height: 85%;
            width: 80vw;
            background:rgb(234, 231, 219);
            padding: 20px;
            border-radius: 20px; /* More natural rounded corners */
            box-shadow: 0px 4px 1000px rgba(0, 0, 0, 0.7);
            z-index: 200;
            overflow-y: auto;
        }
        .details-modal h3{
            font-family: 'Times New Roman', Times, serif;
            font-size: 28px;
            display: block;
            text-align: center;
        }
        .details-modal img{
            width: 50%;
            height: auto;
        }
        .details-modal::-webkit-scrollbar {
            width: 6px; /* Slim scrollbar */
        }

        .details-modal::-webkit-scrollbar-track {
            background: transparent; /* No background */
        }
        .details-modal::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2); /* Subtle dark scrollbar */
            border-radius: 10px; /* Rounded edges */
            
        }
        .details-modal::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.4); /* Slightly darker on hover */
        }
        .details-modal .close-btn{
            position: absolute;
            top: 10px;
            right: 15px;
            cursor: pointer;
            font-size: 20px;
        }

        .close-btn {
            float: right;
            cursor: pointer;
            font-size: 18px;
        }

        textarea {
            width: 100%;
            height: 80px;
        }

    </style>
</head>
<body>
    <div class="navbar">
        <div class="search-bar">
            <input type="text" placeholder="Search">
            <button>X</button>
        </div>
        <div class="museum-title">
            <h1>History of Communication</h1>
        </div>
        <span class="menu-icon" onclick="openMenu()">&#9776;</span>
    </div>

    <div id="side-menu" class="side-menu">
        <span class="close-btn" onclick="closeMenu()">✖</span>
        <br><br><br>
        <a href="MainMenu.php">Home</a>
        <a href="#recommendations">Recommendation</a>
        <a href="HistoryOfCom.html">History of communication</a>
        <a href="#new">What's new</a>
		<a href="Favourite.php">Favourite</a>
		<a href="Profile.html">Profile</a>
        <a href="AboutUs.html">About Us</a>
        <a href="ContactUs.html">Contact Us</a>
		<a href="DMLogin.html">Log Out</a>
    </div>

    <div class="banner">
        <button class="scrollBanner-btn scrollBanner-left" onclick="scrollBannerLeft('banner')">⯇</button>
        
        <div class="banner-container">
            <div class="banner-slide">
                <span class="message" id="message">WELCOME TO DIGITAL MUSEUM</span>
            </div>
            <a href="#recommendations" class="banner-slide">
                <span class="message" id="message">DISCOVER THE EVOLUTION OF TECHNOLOGY!</span>
            </a>
            <a href="HistoryOfCom.html" class="banner-slide">
                <span class="message" id="message">WHAT IS HISTORY OF COMMUNICATION?</span>
            </a>
            <a href="#new" class="banner-slide">
                <span class="message" id="message">NEW ART COLLECTION!</span>
            </a>
            <a href="#recommendations" class="banner-slide">
                <span class="message" id="message">EXPLORE HISTORY TODAY!</span>
            </a>
        </div>
        <button class="scrollBanner-btn scrollBanner-right" onclick="scrollBannerRight('banner')">⯈</button>
    </div>

    <div class="section">
        <h3>Recommendations
            
        </h3>    
        <button class="scroll-btn scroll-left" onclick="scrollToLeft('recommendations')">&#9664</button>
        <div class="cards-container" id="recommendations"></div>
        <button class="scroll-btn scroll-right" onclick="scrollToRight('recommendations')">&#9654</button>
            
        <div id="modal" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal()">✖</span>
                
                <h3></h3>
                <!-- Image Upload -->
                <input type="file" id="imageUpload" accept="image/*">
                <br><br>
                
                <!-- Title Input -->
                <input type="text" id="itemTitle" placeholder="Enter title" style="width: 100%;">
                <br><br>
                
                <!-- Description Input -->
                <textarea id="editTextArea" placeholder="Enter description"></textarea>
                <br><br>

                <button onclick="saveChanges()">Save</button>
            </div>
        </div>

        <!-- Details Modal -->
        <div id="detail-modal-backdrop" class="detail-modal-backdrop">
            <div id="details-modal" class="details-modal">
                <span class="close-btn" onclick="closeDetailsModal()">✖</span>
                <div class="modal-content">
                    <h3 id="modal-title"></h3>
                    <img id="modal-image" src="">
                    <p id="modal-description"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>What's new!
            
        </h3>
        <button class="scrollNew-btn scroll-left" onclick="scrollToLeft('new')">&#9664</button>
        <div class="cards-container" id="new"></div>
        <button class="scrollNew-btn scroll-right" onclick="scrollToRight('new')">&#9654</button>

        <div id="modal" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal()">✖</span>
                
                <h3></h3>
                <!-- Image Upload -->
                <input type="file" id="imageUpload" accept="image/*">
                <br><br>
                
                <!-- Title Input -->
                <input type="text" id="itemTitle" placeholder="Enter title" style="width: 100%;">
                <br><br>
                
                <!-- Description Input -->
                <textarea id="editTextArea" placeholder="Enter description"></textarea>
                <br><br>

                <button onclick="saveChanges()">Save</button>
            </div>
        </div>

        <!-- Details Modal -->
        <div id="detail-modal-backdrop" class="detail-modal-backdrop">
            <div id="details-modal" class="details-modal">
                <span class="close-btn" onclick="closeDetailsModal()">✖</span>
                <div class="modal-content">
                    <h3 id="modal-title"></h3>
                    <img id="modal-image" src="">
                    <p id="modal-description"></p>
                </div>
            </div>
        </div>  
    </div>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <script type="text/javascript">emailjs.init('uSxyjAzXEURtLYRnC')</script>

    <script>  
        //side menu bar
        function openMenu() {
            document.getElementById("side-menu").style.right = "0";
        }
        function closeMenu() {
            document.getElementById("side-menu").style.right = "-250px";
        }

        //BANNER function        
        document.addEventListener("DOMContentLoaded", function () {
            const slides = document.querySelectorAll(".banner-slide");
            const totalSlides = slides.length;
            const bannerContainer = document.querySelector(".banner-container");
            let currentIndex = 0;

            // List of background images for each banner
            const backgrounds = [
                "banner-slide-welcome.jpeg",
                "aboutUs_wallpaper.jpg",
                "HistoryOfCom.webp",
                "Haljesta.jpg",
                "login_background.jpeg"
            ];
            
            // Loop through each slide and set its background dynamically
            slides.forEach((slide, index) => {
                if (backgrounds[index]) {
                    slide.style.backgroundImage = `linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)), url('${backgrounds[index]}')`;
                }
            });

            //autoSlide function
            // function autoSlide(){
            //     currentIndex++;
            //     if(currentIndex >= slides.length){
            //         currentIndex = 0;  //go back to the first slide
            //     }
            //     bannerContainer.scrollTo({ left: slides[currentIndex].offsetLeft, behavior: "smooth" });
            // } 
            function updateSlidePosition() {
                bannerContainer.style.transform = `translateX(${-currentIndex * 100}%)`;
            }

            function scrollBannerLeft() {
                currentIndex = (currentIndex > 0) ? currentIndex - 1 : totalSlides - 1;
                updateSlidePosition();
            }
            function scrollBannerRight() {
                currentIndex = (currentIndex < totalSlides - 1) ? currentIndex + 1 : 0;
                updateSlidePosition();
            }
            // Attach button events
            document.querySelector(".scrollBanner-left").addEventListener("click", scrollBannerLeft);
            document.querySelector(".scrollBanner-right").addEventListener("click", scrollBannerRight);
            updateSlidePosition();
            // Auto-slide every 3 seconds
            //setInterval(scrollBannerRight, 3000);
        });

        //scroll
        function scrollToLeft(section){
            let container = document.getElementById(section);
            container.scrollTo({
                left: container.scrollLeft - 250, // Subtract from current position
                behavior: "smooth"
            });
            // container.scrollBy({left: -230, behavior:"smooth"});  // Scroll left by 200px
        }
        function scrollToRight(section){
            let container = document.getElementById(section);
            container.scrollBy({left: 250, behavior:"smooth"});  // Scroll right by 200px
        }

        //edit modal
        let selectedCard = null;
        let selectedSection = null;

        function loadItems() {
            fetch("api.php")
                .then(response => response.json())
                .then(data => {
                    document.getElementById("recommendations").innerHTML = "";
                    document.getElementById("new").innerHTML = "";

                    data.forEach(item => createCard(item.section, item));
                })
                .catch(error => console.error("Error loading items:", error));
        }


                function saveItems(section) {
            let items = [];
            document.querySelectorAll(`#${section} .card`).forEach(card => {
                let title = card.querySelector("h4").innerText;
                let description = card.querySelector("p").innerText;
                let image = card.querySelector("img").src;

                let storedItems = JSON.parse(localStorage.getItem(section)) || [];
                let existingItem = storedItems.find(i => i.title === title);
        

                items.push({ title, description, image });
            });

            localStorage.setItem(section, JSON.stringify(items));
        }


                function createCard(section, item) {
            let container = document.getElementById(section);
            let card = document.createElement("div");
            card.className = "card";
            card.setAttribute("data-id", item.id); // Store item ID
            card.style.width = "200px";
            card.style.height = "auto";
            card.style.textAlign = "center";
            card.style.padding = "10px";

            // Retrieve favorites from localStorage or database (use PHP for real storage)
            let favorites = JSON.parse(localStorage.getItem("favorites")) || [];
            let isFavorite = favorites.includes(item.id); // Store by ID instead of title

            card.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h4>${item.title}</h4>
                    <span class="favorite-btn" onclick="toggleFavorite(${item.id}, this)" style="cursor: pointer;">
                        ${isFavorite ? '⭐' : '☆'}
                    </span>
                </div>
                <img src="${item.image}" style="width:100%; height:auto; border-radius:10px;">
                <p>${item.description}</p>
                
            `;

            // Open modal on card click (except for buttons)
            card.addEventListener("click", function(event) {
                if (!event.target.closest("button") && event.target.tagName !== "SPAN") {
                    openDetailsModal(item);
                }
            });

            container.appendChild(card);
        }

        function toggleFavorite(itemId, starElement) {
            fetch("toggle_favorite.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "item_id=" + itemId
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "added") {
                    starElement.innerHTML = "⭐"; // Filled star
                } else {
                    starElement.innerHTML = "☆"; // Empty star
                }
            })
            .catch(error => console.error("Error:", error));
        }

                
        function deleteComment(commentId) {
            if (confirm("Are you sure you want to delete this comment?")) {
                fetch('comment_actions.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=delete&comment_id=' + commentId
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    location.reload(); // Refresh page after action
                });
            }
        }

        function toggleMute(userId, muteStatus) {
            let confirmMsg = muteStatus === 1 ? "Mute this user?" : "Unmute this user?";
            if (confirm(confirmMsg)) {
                fetch('comment_actions.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=mute&user_id=' + userId + '&mute=' + muteStatus
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    location.reload(); // Refresh page after action
                });
            }
        }

        function addNewItem(section) {
            selectedCard = null; // This means we're adding a new item, not editing an existing one.
            selectedSection = section;

            document.getElementById("modal").style.display = "block";
            document.getElementById("modal").querySelector("h3").innerText = "Add New Item";
            
            document.getElementById("itemTitle").value = ""; // Clear input fields
            document.getElementById("editTextArea").value = "";
            document.getElementById("imageUpload").value = "";
            
        }
		function updateDatabase(title, description, image) {
            let section = selectedSection;
            let id = selectedCard ? selectedCard.getAttribute("data-id") : null;

            if (!id) {
                console.error("Error: No ID found for update.");
                alert("Error: Unable to update item, missing ID.");
                return;
            }

            console.log("Updating item:", { id, section, title, description, image });

            fetch("api.php", {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id, section, title, description, image })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                selectedCard.querySelector("h4").innerText = title;
                selectedCard.querySelector("p").innerText = description;
                selectedCard.querySelector("img").src = image;
                closeModal();
            })
            .catch(error => console.error("Error updating item:", error));
        }

        function editItem(button, section) {
            selectedCard = button.parentElement;
            selectedSection = section;

            document.getElementById("modal").querySelector("h3").innerText = "Edit information";
            document.getElementById("itemTitle").value = selectedCard.querySelector("h4").innerText;
            document.getElementById("editTextArea").value = selectedCard.querySelector("p").innerText;
            document.getElementById("imageUpload").value = "";

            document.getElementById("modal").style.display = "block";
        }

        function saveChanges() {
            let title = document.getElementById("itemTitle").value.trim();
            let description = document.getElementById("editTextArea").value.trim();
            let fileInput = document.getElementById("imageUpload");

            if (!title || !description) {
                alert("Please complete the information.");
                return;
            }

            let imageBase64 = "";
            if (fileInput.files.length > 0) {
                let reader = new FileReader();
                reader.onload = function (event) {
                    imageBase64 = event.target.result;
                    if (selectedCard) {
                        updateDatabase(title, description, imageBase64);
                    } else {
                        sendToDatabase(title, description, imageBase64);
                    }
                };
                reader.readAsDataURL(fileInput.files[0]);
            } else {
                if (selectedCard) {
                    updateDatabase(title, description, selectedCard.querySelector("img").src);
                } else {
                    sendToDatabase(title, description, "icon_profile.png");
                }
            }
        }

        function sendToDatabase(title, description, image) {
            let section = selectedSection;
            fetch("api.php", {
                method: "POST",
                body: JSON.stringify({ section, title, description, image }),
                headers: { "Content-Type": "application/json" }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                createCard(section, { title, description, image });
                closeModal();
            })
            .catch(error => console.error("Error saving item:", error));
        }


                function deleteItem(button, section) {
            if (!confirm("Are you sure you want to delete this item?")) return;

            let card = button.parentElement;
            let id = card.getAttribute("data-id"); // Ensure ID is stored in the card

            fetch("api.php", {
                method: "DELETE",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ id: id }) // Send the correct ID
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                card.remove();
            })
            .catch(error => console.error("Error deleting item:", error));
        }

        function closeModal() {
            document.getElementById("modal").style.display = "none";
        }
                
            function openDetailsModal(itemId) {
            let modal = document.getElementById("details-modal");
            let modalBackdrop = document.getElementById("detail-modal-backdrop");

            // Set modal content with placeholders
            modal.innerHTML = `
                <span class="close-btn" onclick="closeDetailsModal()" 
                    style="position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer; z-index: 1000;">
                    ✖
                </span>

                <div style="text-align: center;">
                    <h3 id="modal-title">${itemId.title}</h3>
                    <img id="modal-image" src="${itemId.image}" 
                        style="width:30%; height:auto; border-radius:10px; display: block; margin: 0 auto;">
                    
                    <div style="max-height: 200px; overflow-y: auto; padding: 10px; border: 1px solid #ddd; text-align: center;">
                        <p id="modal-description">${itemId.description}</p>
                    </div>

                    <h2>Comments</h2>
                    <div id="comments-section">Loading comments...</div>

                    <!-- Comment Form -->
                    <form id="comment-form">
                        <textarea name="comment" id="comment-text" placeholder="Write your comment..." required></textarea>
                        <button type="submit">Post Comment</button>
                    </form>
                </div>
            `;

            // Fetch comments dynamically
            fetch("fetch_comments.php?item_id=" + itemId.id)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("comments-section").innerHTML = data;
                });

            // Handle comment submission (Reopen Modal)
            setTimeout(() => {
                let commentForm = document.getElementById("comment-form");
                commentForm.addEventListener("submit", function(e) {
                    e.preventDefault();
                    let commentText = document.getElementById("comment-text").value;

                    fetch("add_comment.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: "item_id=" + itemId.id + "&comment=" + encodeURIComponent(commentText)
                    })
                    .then(() => {
                        closeDetailsModal(); // Close the modal
                        setTimeout(() => openDetailsModal(itemId), 200); // Reopen it after 200ms
                    });
                });
            }, 100);

            // Show modal
            modalBackdrop.style.display = "block";
            modal.style.display = "block";
        }

        function closeDetailsModal() {
            document.getElementById("details-modal").style.display = "none";
            document.getElementById("detail-modal-backdrop").style.display = "none";
        }

        function closeDetailsModal() {
            document.getElementById("detail-modal-backdrop").style.display = "none";
            document.getElementById("details-modal").style.display = "none";
        }

        // Click on backdrop closes the modal
        document.getElementById("detail-modal-backdrop").addEventListener("click", function(event) {
            if (event.target === this) {
                closeDetailsModal();
            }
        });

        window.onload = loadItems;
    </script>
</body>
</html>
