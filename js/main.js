import { handleRegister, handleLogin, handleAdminLogin, handleResetPassword, handleDeleteUser, handleAddUser, loadUserList } from './handlers.js';
import { 
    showCustomAlert, 
    switchForm, 
    showAdminPanel, 
    updateUserTable, 
    updatePagination
} from './ui.js';

// 页面加载时初始化
document.addEventListener('DOMContentLoaded', () => {
    // 绑定表单提交事件
    const loginForm = document.getElementById('loginFormElement');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }

    const registerForm = document.getElementById('registerFormElement');
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegister);
    }

    const forgotForm = document.getElementById('forgotFormElement');
    if (forgotForm) {
        forgotForm.addEventListener('submit', handleResetPassword);
    }
    
    const adminForm = document.getElementById('adminFormElement');
    if (adminForm) {
        adminForm.addEventListener('submit', handleAdminLogin);
    }

    // 绑定管理员面板事件
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        const submitBtn = addUserForm.querySelector('.submit-btn');
        if (submitBtn) {
            submitBtn.addEventListener('click', handleAddUser);
        }
    }

    // 绑定搜索事件
    const searchInput = document.querySelector('.search-box input');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            loadUserList(1, e.target.value);
        });
    }

    // 绑定刷新列表事件
    const refreshBtn = document.querySelector('.refresh-btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', () => loadUserList(1));
    }

    // 绑定分页事件
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    if (prevBtn && nextBtn) {
        let currentPage = 1;
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                loadUserList(--currentPage);
            }
        });
        nextBtn.addEventListener('click', () => {
            loadUserList(++currentPage);
        });
    }

    // 将所有需要的函数暴露给全局
    Object.assign(window, {
        showCustomAlert,
        switchForm,
        showAdminPanel,
        handleDeleteUser,
        handleAddUser,
        loadUserList
    });
}); 