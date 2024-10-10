document.addEventListener("DOMContentLoaded", function() {
    // Sidebar navigation
    const links = document.querySelectorAll(".sidebar ul li a");
    const sections = document.querySelectorAll(".content-section");

    links.forEach(link => {
        link.addEventListener("click", function(e) {
            e.preventDefault();

            links.forEach(link => link.classList.remove("active"));
            this.classList.add("active");

            sections.forEach(section => section.style.display = "none");

            const targetId = this.getAttribute("href").substring(1);
            document.getElementById(targetId).style.display = "block";
        });
    });

    sections.forEach(section => section.style.display = "none");
    document.querySelector(".content-section").style.display = "block";

    // Announcement form toggle
    const addAnnouncementButton = document.getElementById("announcementBtn");
    const announcementForm = document.getElementById("announcementForm");

    if (addAnnouncementButton && announcementForm) {
        addAnnouncementButton.addEventListener("click", function() {
            if (announcementForm.style.display === "none" || announcementForm.style.display === "") {
                announcementForm.style.display = "block";
            } else {
                announcementForm.style.display = "none";
            }
        });
    }

    // Add event listener for delete buttons
    document.querySelectorAll(".delete-btn").forEach(button => {
        button.addEventListener("click", function() {
            // Confirm deletion
            const confirmDelete = confirm("Are you sure you want to delete this announcement?");
            if (confirmDelete) {
                // Get the ID of the announcement to delete
                const announcementId = this.getAttribute("data-id").split('_')[1]; // Extract numeric ID

                // Find and remove the announcement element
                const announcement = document.getElementById(this.getAttribute("data-id"));
                if (announcement) {
                    announcement.remove();

                    // Make an AJAX request to delete the announcement from the server
                    fetch('delete_announcement.php', {
                        method: 'POST',
                        body: JSON.stringify({ id: announcementId }),
                        headers: { 'Content-Type': 'application/json' }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Announcement deleted successfully');
                        } else {
                            console.error('Error deleting announcement:', data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            }
        });
    });
    document.getElementById('logoutBtn').addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Logout button clicked'); // Debugging line
        window.location.href = 'logout.php';
    });

    let secnavBtn = document.getElementById("secnavbarBtn");
    let secnavbar = document.getElementsByClassName("sidebar")[0];
    
   
});
