<div class="d-flex">
    <!-- Menú lateral -->
    <nav id="sidebar" class="bg-dark text-white p-3 vh-100 d-flex flex-column align-items-center" style="width: 80px; position: fixed; transition: width 0.3s;">
        <ul class="nav flex-column w-100">
            <li class="nav-item text-center">
                <a href="#" id="toggleMenu" class="nav-link text-white">
                    <i class="fa fa-list fs-3"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center" href="{{ route('dashboard') }}">
                    <i class="fa fa-home fs-4"></i>
                    <span class="ms-2 d-none">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center" href="{{ route('usuarios.lstUsuarios') }}">
                    <i class="fa fa-users fs-4"></i>
                    <span class="ms-2 d-none">Usuarios</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center" href="{{ route('usuarios.lstUsuarios') }}">
                    <i class="fa fa-shield fs-4"></i>
                    <span class="ms-2 d-none">Roles</span>
                </a>
            </li>
        </ul>
    </nav>
    <script>
    document.getElementById('toggleMenu').addEventListener('click', function () {
        let sidebar = document.getElementById('sidebar');
        let mainContent = document.getElementById('main-content');
        let spans = sidebar.querySelectorAll('span');

        if (sidebar.style.width === '80px' || sidebar.style.width === '') {
            sidebar.style.width = '250px';
            mainContent.style.marginLeft = '250px';
            mainContent.style.width = 'calc(100% - 250px)';
            spans.forEach(span => span.classList.remove('d-none'));
        } else {
            sidebar.style.width = '80px';
            mainContent.style.marginLeft = '80px';
            mainContent.style.width = 'calc(100% - 80px)';
            spans.forEach(span => span.classList.add('d-none'));
        }
    });
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
