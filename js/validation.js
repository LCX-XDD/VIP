// 表单验证类
export class FormValidator {
    static validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    static validatePassword(password) {
        return password.length >= 6 && password.length <= 20;
    }

    static validateUsername(username) {
        return username.length >= 3 && username.length <= 20;
    }

    static validateConfirmPassword(password, confirmPassword) {
        return password === confirmPassword;
    }
}

// 导出验证函数
export const validateForm = {
    email: FormValidator.validateEmail,
    password: FormValidator.validatePassword,
    username: FormValidator.validateUsername,
    confirmPassword: FormValidator.validateConfirmPassword
}; 