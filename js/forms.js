// 表单切换函数
function switchForm(formId) {
    // 移除所有表单的active类和display:none样式
    document.querySelectorAll('.form-section').forEach(form => {
        form.classList.remove('active');
        form.style.display = 'none';
    });

    // 处理管理员登录表单的特殊显示
    if (formId === 'adminForm') {
        const adminForm = document.getElementById('adminForm');
        adminForm.style.display = 'block';
        adminForm.classList.add('active');
    } else {
        document.getElementById('adminForm').style.display = 'none';
        document.getElementById('adminForm').classList.remove('active');
    }
    
    // 添加active类到目标表单并显示
    const targetForm = document.getElementById(formId);
    targetForm.classList.add('active');
    if (formId !== 'adminForm') {
        targetForm.style.display = 'block';
    }
    
    // 更新页面标题
    const titles = {
        'loginForm': 'LCX - 登录',
        'registerForm': 'LCX - 注册',
        'forgotForm': 'LCX - 找回密码',
        'adminForm': 'LCX - 管理员登录',
        'adminPanel': 'LCX - 用户管理',
        'addUserForm': 'LCX - 添加用户'
    };
    document.title = titles[formId];
}

// 登录表单处理
async function handleLogin(event) {
    event.preventDefault();
    const username = document.getElementById('login-username').value;
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;

    // 检查用户名和邮箱是否都为空
    if (!username && !email) {
        showCustomAlert('请输入用户名或邮箱');
        return;
    }

    try {
        const response = await fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, email, password })
        });

        const data = await response.json();
        if (data.status === 'success') {
            showCustomAlert('登录成功！', 'success');
            // 这里可以添加登录成功后的跳转逻辑
        } else {
            showCustomAlert(data.message);
        }
    } catch (error) {
        showCustomAlert('登录请求失败，请稍后重试');
        console.error('Error:', error);
    }
}

// 注册表单处理
async function handleRegister(event) {
    event.preventDefault();
    const username = document.getElementById('register-username').value;
    const email = document.getElementById('register-email').value;
    const password = document.getElementById('register-password').value;
    const confirmPassword = document.getElementById('register-confirm').value;

    if (password !== confirmPassword) {
        showCustomAlert('两次输入的密码不一致');
        return;
    }

    try {
        const response = await fetch('register.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, email, password })
        });

        const data = await response.json();
        if (data.status === 'success') {
            showCustomAlert('注册成功！', 'success');
            setTimeout(() => {
                switchForm('loginForm');
            }, 1500);
        } else {
            showCustomAlert(data.message);
        }
    } catch (error) {
        showCustomAlert('注册请求失败，请稍后重试');
        console.error('Error:', error);
    }
}

// 重置密码表单处理
async function handleResetPassword(event) {
    event.preventDefault();
    const email = document.getElementById('forgot-email').value;
    const newPassword = document.getElementById('forgot-new-password').value;
    const confirmPassword = document.getElementById('forgot-confirm').value;

    if (newPassword !== confirmPassword) {
        showCustomAlert('两次输入的密码不一致');
        return;
    }

    try {
        const response = await fetch('reset_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email, new_password: newPassword })
        });

        const data = await response.json();
        if (data.status === 'success') {
            showCustomAlert('密码重置成功！', 'success');
            setTimeout(() => {
                switchForm('loginForm');
            }, 1500);
        } else {
            showCustomAlert(data.message);
        }
    } catch (error) {
        showCustomAlert('密码重置请求失败，请稍后重试');
        console.error('Error:', error);
    }
}

// 页面加载时设置初始标题
document.addEventListener('DOMContentLoaded', () => {
    document.title = 'LCX - 登录';
}); 