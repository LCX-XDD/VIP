// 管理员登录处理
async function handleAdminLogin(event) {
    event.preventDefault();
    const username = document.getElementById('admin-username').value;
    const password = document.getElementById('admin-password').value;

    try {
        const response = await fetch('php/admin_login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, password })
        });

        const data = await response.json();
        if (data.status === 'success') {
            showCustomAlert('登录成功！', 'success');
            showAdminPanel();
            refreshUserList();
        } else {
            showCustomAlert(data.message);
        }
    } catch (error) {
        showCustomAlert('登录请求失败，请稍后重试');
        console.error('Error:', error);
    }
}

// 管理员退出登录
async function handleLogout() {
    if (!confirm('确定要退出登录吗？')) return;

    try {
        const response = await fetch('php/admin_logout.php');
        const data = await response.json();
        if (data.status === 'success') {
            showCustomAlert('已成功退出登录', 'success');
            switchForm('loginForm');
        } else {
            showCustomAlert(data.message);
        }
    } catch (error) {
        showCustomAlert('退出登录失败，请稍后重试');
        console.error('Error:', error);
    }
}

// 显示管理员面板
function showAdminPanel() {
    switchForm('adminPanel');
    refreshUserList();
}

// 显示添加用户表单
function showAddUserForm() {
    document.getElementById('addUserForm').classList.add('show');
}

// 关闭添加用户表单
function closeAddUserForm() {
    document.getElementById('addUserForm').classList.remove('show');
    // 清空表单
    document.getElementById('newUsername').value = '';
    document.getElementById('newPassword').value = '';
    document.getElementById('newEmail').value = '';
}

// 提交添加用户
async function submitAddUser() {
    const username = document.getElementById('newUsername').value;
    const password = document.getElementById('newPassword').value;
    const email = document.getElementById('newEmail').value;

    // 验证表单
    if (!username || !password || !email) {
        showCustomAlert('请填写所有必填字段', 'error');
        return;
    }

    try {
        const response = await fetch('php/add_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                username,
                password,
                email
            })
        });

        const data = await response.json();
        
        if (data.status === 'success') {
            showCustomAlert('用户添加成功', 'success');
            closeAddUserForm();
            refreshUserList(); // 刷新用户列表
        } else {
            showCustomAlert(data.message || '添加用户失败', 'error');
        }
    } catch (error) {
        console.error('添加用户时出错:', error);
        showCustomAlert('添加用户失败，请稍后重试', 'error');
    }
}

// 刷新用户列表
async function refreshUserList() {
    try {
        const response = await fetch('php/get_users.php');
        const data = await response.json();
        
        if (data.status === 'success') {
            const userTableBody = document.getElementById('userTableBody');
            userTableBody.innerHTML = '';
            
            data.data.users.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.username}</td>
                    <td>${user.email}</td>
                    <td>${user.created_at}</td>
                    <td>
                        <button class="action-btn edit-btn" onclick="editUser(${user.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete-btn" onclick="deleteUser(${user.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                userTableBody.appendChild(row);
            });
            
            // 更新分页信息
            document.getElementById('totalUsers').textContent = data.data.total;
            document.getElementById('currentPage').textContent = data.data.page;
            
            // 更新分页按钮状态
            document.getElementById('prevBtn').disabled = data.data.page <= 1;
            document.getElementById('nextBtn').disabled = data.data.page * data.data.limit >= data.data.total;
        } else {
            showCustomAlert(data.message || '获取用户列表失败', 'error');
        }
    } catch (error) {
        console.error('获取用户列表时出错:', error);
        showCustomAlert('获取用户列表失败，请稍后重试', 'error');
    }
}

// 切换页面
function changePage(direction) {
    const currentPage = parseInt(document.getElementById('currentPage').textContent);
    const newPage = currentPage + direction;
    
    if (newPage < 1) return;
    
    // 获取新页面的数据
    refreshUserList(newPage);
}

// 搜索用户
function searchUsers(keyword) {
    // TODO: 实现搜索功能
    console.log('搜索关键词:', keyword);
}

// 编辑用户
function editUser(userId) {
    // TODO: 实现编辑功能
    console.log('编辑用户:', userId);
}

// 删除用户
function deleteUser(userId) {
    // TODO: 实现删除功能
    console.log('删除用户:', userId);
}

// 页面加载完成后刷新用户列表
document.addEventListener('DOMContentLoaded', () => {
    refreshUserList();
});

// 会话检查处理
function handleSessionResponse(data) {
    if (data.redirect) {
        showCustomAlert('会话已过期，请重新登录');
        switchForm('adminForm');
        return true;
    }
    return false;
}

// 添加用户处理
async function handleAddUser(event) {
    event.preventDefault();
    const username = document.getElementById('add-username').value;
    const email = document.getElementById('add-email').value;
    const password = document.getElementById('add-password').value;

    try {
        const response = await fetch('php/add_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, email, password })
        });

        const data = await response.json();
        if (data.status === 'success') {
            showCustomAlert('添加用户成功！', 'success');
            showAdminPanel();
            refreshUserList();
        } else {
            if (!handleSessionResponse(data)) {
                showCustomAlert(data.message);
            }
        }
    } catch (error) {
        showCustomAlert('添加用户失败，请稍后重试');
        console.error('Error:', error);
    }
}

// 管理员相关功能

// 显示退出确认弹窗
window.showLogoutModal = function() {
    console.log('显示退出弹窗');
    document.getElementById('logoutOverlay').classList.add('show');
    document.getElementById('logoutModal').classList.add('show');
};

// 关闭退出确认弹窗
window.closeLogoutModal = function() {
    console.log('关闭退出弹窗');
    document.getElementById('logoutOverlay').classList.remove('show');
    document.getElementById('logoutModal').classList.remove('show');
};

// 确认退出
window.confirmLogout = function() {
    console.log('确认退出');
    // 清除管理员会话
    fetch('php/admin_logout.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // 跳转到登录页面
                window.location.href = 'index.html';
            } else {
                showCustomAlert('退出失败，请重试');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showCustomAlert('退出失败，请重试');
        });
}; 