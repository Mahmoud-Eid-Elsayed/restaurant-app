// Function to load HTML template
async function loadTemplate(templatePath) {
    try {
        const response = await fetch(templatePath);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.text();
    } catch (error) {
        console.error('Error loading template:', error);
        return '';
    }
}

// Get current page URL
const currentPage = window.location.pathname.split('/').pop() || 'index.html';

// Initialize sidebar and profile dropdown
async function initializeLayout() {
    // Load and insert sidebar
    const sidebarTemplate = await loadTemplate('templates/sidebar.html');
    document.getElementById('sidebar').innerHTML = sidebarTemplate;

    // Set active nav link
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });

    // Generate profile dropdown
    const dropdownContent = `
        <div class="dropdown">
            <button class="btn dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="../../../assets/images/users/mitchell.jpg" alt="Profile" class="profile-pic">
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                <li><a class="dropdown-item" href="profile.html"><i class="fas fa-user"></i> Profile</a></li>
                <li><a class="dropdown-item" href="inbox.html"><i class="fas fa-inbox"></i> Inbox</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    `;
    document.querySelector('.profile-dropdown').innerHTML = dropdownContent;
}

// Toggle sidebar function
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeLayout();

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 991.98) {
            document.getElementById('sidebar').classList.remove('active');
        }
    });
}); 