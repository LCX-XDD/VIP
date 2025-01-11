// 注册用户
function registerUser() {
    const username = document.getElementById('register-username').value;
    const email = document.getElementById('register-email').value;
    const password = document.getElementById('register-password').value;

    const existingUsers = JSON.parse(localStorage.getItem('users')) || [];
    const userExists = existingUsers.some(user => user.username === username || user.email === email);

    if (userExists) {
        alert('用户名或邮箱已存在');
        return;
    }

    const newUser = { username, email, password };
    existingUsers.push(newUser);
    localStorage.setItem('users', JSON.stringify(existingUsers));

    alert('注册成功');
}

// 登录用户
function loginUser() {
    const username = document.getElementById('login-username').value;
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;

    const existingUsers = JSON.parse(localStorage.getItem('users')) || [];
    const user = existingUsers.find(user => (user.username === username || user.email === email) && user.password === password);

    if (user) {
        alert('登录成功');
        // 执行登录后的操作，如跳转页面
    } else {
        alert('用户名、邮箱或密码错误');
    }
}

// 导出所有函数
export {
    registerUser,
    loginUser
}; 