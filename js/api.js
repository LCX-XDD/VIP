// API 基础 URL
const API_BASE_URL = 'http://localhost/VIP/php';

// 通用请求函数
async function request(endpoint, method = 'POST', data = null) {
    try {
        const response = await fetch(`${API_BASE_URL}/${endpoint}.php`, {
            method: method,
            headers: {
                'Content-Type': 'application/json; charset=utf-8',
                'Accept': 'application/json',
                'Cache-Control': 'no-cache'
            },
            body: data ? JSON.stringify(data) : null,
            credentials: 'same-origin'
        });

        // 首先检查响应状态
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log('服务器响应:', result); // 添加调试日志

        // 检查服务器返回的状态
        if (result.status === 'error') {
            throw new Error(result.message);
        }

        return result;
    } catch (error) {
        console.error('请求错误:', error);
        throw error;
    }
}

// 用户注册
async function register(username, email, password, confirmPassword) {
    return request('register', 'POST', {
        username,
        email,
        password,
        confirm_password: confirmPassword
    });
}

// 用户登录
async function login(username, email, password) {
    try {
        const result = await request('login', 'POST', {
            username,
            email,
            password
        });
        
        // 检查登录结果
        if (result.status === 'error') {
            throw new Error(result.message);
        }
        
        return result;
    } catch (error) {
        console.error('登录失败:', error);
        throw error;
    }
}

// 管理员登录
async function adminLogin(username, password) {
    return request('admin_login', 'POST', {
        username,
        password
    });
}

// 重置密码
async function resetPassword(email, newPassword, confirmPassword) {
    try {
        return await request('forgot', 'POST', {
            email,
            new_password: newPassword,
            confirm_password: confirmPassword
        });
    } catch (error) {
        console.error('Reset password error:', error);
        throw error;
    }
}

// 获取用户列表
async function getUserList(page = 1, search = '') {
    return request('get_users', 'POST', {
        page,
        search
    });
}

// 添加新用户
async function addUser(username, email, password) {
    return request('add_user', 'POST', {
        username,
        email,
        password
    });
}

// 删除用户
async function deleteUser(userId) {
    return request('delete_user', 'POST', {
        user_id: userId
    });
}

// 导出所有函数
export {
    register,
    login,
    adminLogin,
    resetPassword,
    getUserList,
    addUser,
    deleteUser
}; 