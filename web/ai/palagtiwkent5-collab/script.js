function changeMessage() {
    const messages = [
        "Keep learning every day!",
        "Small progress is still progress.",
        "Believe in yourself.",
        "Success starts with one step.",
        "Grow as you go!"
    ];

    const random = Math.floor(Math.random() * messages.length);

    document.getElementById("message").textContent = messages[random];
}
