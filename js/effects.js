// 雪花效果
function createSnowflake() {
    const snowflake = document.createElement('div');
    snowflake.className = 'snowflake';
    snowflake.textContent = '❄';
    
    const startX = Math.random() * window.innerWidth;
    const duration = 5000 + Math.random() * 5000;
    const endX = startX + (Math.random() * 200 - 100);
    
    snowflake.style.setProperty('--start-x', `${startX}px`);
    snowflake.style.setProperty('--end-x', `${endX}px`);
    snowflake.style.left = `${startX}px`;
    snowflake.style.animationDuration = `${duration}ms`;
    
    document.getElementById('snowflakes').appendChild(snowflake);
    
    setTimeout(() => {
        snowflake.remove();
    }, duration);
}

// 定期创建雪花
setInterval(createSnowflake, 300); 