document.addEventListener("DOMContentLoaded", function () {
    // Get the elements
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    
    // Only add event listener if both elements exist
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", function () {
            sidebar.classList.toggle("active");
            
            // Optionally save the state
            const isActive = sidebar.classList.contains("active");
            localStorage.setItem("sidebarState", isActive ? "active" : "inactive");
        });

        // Restore sidebar state from localStorage if it exists
        const savedState = localStorage.getItem("sidebarState");
        if (savedState === "active") {
            sidebar.classList.add("active");
        }
    }

    // Add smooth transition after initial state is set
    setTimeout(() => {
        if (sidebar) {
            sidebar.style.transition = "all 0.3s ease";
        }
    }, 100);
});

// Add click outside to close sidebar on mobile
document.addEventListener("click", function(event) {
    const sidebar = document.getElementById("sidebar");
    const sidebarToggle = document.getElementById("sidebarToggle");
    
    if (sidebar && sidebarToggle) {
        const isClickInside = sidebar.contains(event.target) || sidebarToggle.contains(event.target);
        
        if (!isClickInside && window.innerWidth <= 768 && sidebar.classList.contains("active")) {
            sidebar.classList.remove("active");
            localStorage.setItem("sidebarState", "inactive");
        }
    }
});

// Handle window resize
window.addEventListener("resize", function() {
    const sidebar = document.getElementById("sidebar");
    
    if (sidebar) {
        if (window.innerWidth > 768) {
            sidebar.classList.remove("active");
        }
    }
});