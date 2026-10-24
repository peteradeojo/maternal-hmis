const getRandomInt = (min, max) => {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function getRandomColor(alpha = 0.5) {
    const r = getRandomInt(0, 255);
    const g = getRandomInt(0, 255);
    const b = getRandomInt(0, 255);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

export { getRandomColor };
