import { register, login, adminLogin, resetPassword, getUserList, addUser, deleteUser } from './api.js';
import { showCustomAlert, switchForm, showAdminPanel, updateUserTable, updatePagination } from './ui.js';
import { validateForm } from './validation.js';

// 处理用户注册
async function handleRegister(event) {
    event.preventDefault();
    
    const username = document.getElementById('register-username').value;
    const email = document.getElementById('register-email').value;
    const password = document.getElementById('register-password').value;
    const confirmPassword = document.getElementById('register-confirm').value;
    
    try {
        // 添加表单验证
        if (!validateForm.username(username)) {
            throw new Error('用户名长度必须在3-20个字符之间');
        }
        if (!validateForm.email(email)) {
            throw new Error('请输入有效的邮箱地址');
        }
        if (!validateForm.password(password)) {
            throw new Error('密码长度必须在6-20个字符之间');
        }
        if (!validateForm.confirmPassword(password, confirmPassword)) {
            throw new Error('两次输入的密码不一致');
        }

        await register(username, email, password, confirmPassword);
        showCustomAlert('注册成功！', 'success');
        switchForm('loginForm');
    } catch (error) {
        showCustomAlert(error.message, 'error');
    }
}

// 处理用户登录
async function handleLogin(event) {
    event.preventDefault();
    
    const username = document.getElementById('login-username').value.trim();
    const email = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value;
    
    // 验证输入
    if (!password) {
        showCustomAlert('请输入密码', 'error');
        return;
    }
    if (!username && !email) {
        showCustomAlert('请至少输入用户名或邮箱', 'error');
        return;
    }

    try {
        const response = await fetch('php/login.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: username,
                email: email,
                password: password
            })
        });

        const result = await response.json();

        if (result.status === 'success') {
            localStorage.setItem('userInfo', JSON.stringify(result.data.user));
            showCustomAlert('登录成功！', 'success');
            
            // 延迟跳转到用户页面
            setTimeout(() => {
                window.location.href = 'user.html';
            }, 1000);
        } else {
            throw new Error(result.message || '登录失败');
        }
    } catch (error) {
        showCustomAlert(error.message, 'error');
    }
}

// 处理管理员登录
async function handleAdminLogin(event) {
    event.preventDefault();
    
    const username = document.getElementById('admin-username').value;
    const password = document.getElementById('admin-password').value;
    
    try {
        const result = await adminLogin(username, password);
        showCustomAlert('管理员登录成功！', 'success');
        document.getElementById('adminUsername').textContent = username;
        showAdminPanel();
        loadUserList();
    } catch (error) {
        showCustomAlert(error.message, 'error');
    }
}

// 处理密码重置
async function handleResetPassword(event) {
    event.preventDefault();
    
    const email = document.getElementById('forgot-email').value;
    const newPassword = document.getElementById('forgot-new-password').value;
    const confirmPassword = document.getElementById('forgot-confirm').value;
    
    try {
        await resetPassword(email, newPassword, confirmPassword);
        showCustomAlert('密码重置成功！请使用新密码登录。', 'success');
        switchForm('loginForm');
    } catch (error) {
        showCustomAlert(error.message, 'error');
    }
}

// 处理用户列表加载
async function loadUserList(page = 1, search = '') {
    try {
        const result = await getUserList(page, search);
        updateUserTable(result.data.users);
        updatePagination(result.data.total_pages, page);
    } catch (error) {
        showCustomAlert(error.message, 'error');
    }
}

// 处理添加用户
async function handleAddUser(event) {
    event.preventDefault();
    
    const username = document.getElementById('newUsername').value;
    const email = document.getElementById('newEmail').value;
    const password = document.getElementById('newPassword').value;
    
    try {
        await addUser(username, email, password);
        showCustomAlert('用户添加成功！', 'success');
        closeAddUserForm();
        loadUserList();
    } catch (error) {
        showCustomAlert(error.message, 'error');
    }
}

// 处理删除用户
async function handleDeleteUser(userId) {
    if (!confirm('确定要删除这个用户吗？')) {
        return;
    }
    
    try {
        await deleteUser(userId);
        showCustomAlert('用户删除成功！', 'success');
        loadUserList();
    } catch (error) {
        showCustomAlert(error.message, 'error');
    }
}

// 导出所有处理函数
export {
    handleLogin,
    handleRegister,
    handleAdminLogin,
    handleResetPassword,
    handleAddUser,
    handleDeleteUser,
    loadUserList
}; 