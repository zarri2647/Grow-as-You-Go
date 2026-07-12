function changeMessage() {
    const messages = [
        "Keep learning every day!",
        "Small progress is still progress.",
        "Believe in yourself.",
        "Success starts with one step.",
        "Grow as you go!",
        `<a href="https://web.facebook.com/share/v/1BdVYZrRoF/" target="_blank">Your potential is limitless. Click to unlock your daily reward!</a>`
    ];

    const random = Math.floor(Math.random() * messages.length);

    document.getElementById("message").innerHTML = messages[random];
}
