// UI 相关函数
const alertQueue = [];
let currentAlertTimer = null;

// 显示自定义提示
function showCustomAlert(message, type = 'info') {
    const alertElement = document.getElementById('customAlert');
    const messageElement = alertElement.querySelector('.custom-alert-message');
    const iconElement = alertElement.querySelector('.custom-alert-icon');
    
    // 设置消息
    messageElement.textContent = message;
    
    // 设置图标和类型
    if (type === 'success') {
        iconElement.innerHTML = '✓';
        alertElement.className = 'custom-alert success';
    } else if (type === 'error') {
        iconElement.innerHTML = '✕';
        alertElement.className = 'custom-alert error';
    } else {
        iconElement.innerHTML = 'ℹ';
        alertElement.className = 'custom-alert';
    }
    
    // 显示弹窗
    alertElement.style.display = 'flex';
    
    // 触发重排以启动动画
    alertElement.offsetHeight;
    alertElement.classList.add('show');
    
    // 3秒后隐藏
    setTimeout(() => {
        alertElement.classList.remove('show');
        alertElement.classList.add('hide');
        
        // 等待滑出动画完成后隐藏元素
        setTimeout(() => {
            alertElement.style.display = 'none';
            alertElement.classList.remove('hide');
        }, 300);
    }, 3000);
}

// 移除提示框的辅助函数
function removeAlert(alertBox) {
    if (!alertBox || !alertBox.parentNode) return;
    
    // 添加隐藏类以触发滑出动画
    alertBox.classList.add('hide');
    alertBox.classList.remove('show');
    
    // 等待动画完成后移除元素
    setTimeout(() => {
        if (alertBox.parentNode) {
            alertBox.parentNode.removeChild(alertBox);
            // 重新调整其他提示框的位置
            document.querySelectorAll('.custom-alert').forEach((alert, index) => {
                alert.style.top = `${20 + index * 70}px`;
            });
        }
    }, 500); // 与 CSS 中的过渡时间匹配
}

// 表单切换函数
function switchForm(formId) {
    // 隐藏所有表单
    document.querySelectorAll('.form-section').forEach(form => {
        form.classList.remove('active');
    });
    // 显示目标表单
    document.getElementById(formId).classList.add('active');
    
    // 更新页面标题
    const titles = {
        'loginForm': 'LCX - 登录',
        'registerForm': 'LCX - 注册',
        'forgotForm': 'LCX - 找回密码',
        'adminForm': 'LCX - 管理员登录'
    };
    document.title = titles[formId] || 'LCX';
}

// 显示管理员面板
function showAdminPanel() {
    document.querySelector('.form-container').style.display = 'none';
    document.getElementById('adminPanel').style.display = 'block';
}

// 更新用户表格
function updateUserTable(users) {
    const tbody = document.getElementById('userTableBody');
    tbody.innerHTML = '';
    
    users.forEach(user => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${user.id}</td>
            <td>${user.username}</td>
            <td>${user.email}</td>
            <td>${user.created_at}</td>
            <td>
                <button class="delete-btn" data-user-id="${user.id}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    // 绑定删除按钮事件
    tbody.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const userId = btn.dataset.userId;
            if (window.handleDeleteUser) {
                window.handleDeleteUser(userId);
            }
        });
    });
}

// 更新分页
function updatePagination(totalPages, currentPage) {
    document.getElementById('currentPage').textContent = currentPage;
    document.getElementById('prevBtn').disabled = currentPage <= 1;
    document.getElementById('nextBtn').disabled = currentPage >= totalPages;
}

// 显示退出确认弹窗
export function showLogoutModal() {
    document.getElementById('logoutOverlay').style.display = 'block';
    document.getElementById('logoutModal').style.display = 'block';
}

// 关闭退出确认弹窗
export function closeLogoutModal() {
    document.getElementById('logoutOverlay').style.display = 'none';
    document.getElementById('logoutModal').style.display = 'none';
}

// 确认退出
export function confirmLogout() {
    localStorage.removeItem('userInfo');
    window.location.href = 'index.html';
}

// 导出所有函数
export {
    showCustomAlert,
    switchForm,
    showAdminPanel,
    updateUserTable,
    updatePagination
}; 