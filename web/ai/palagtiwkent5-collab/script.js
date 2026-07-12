function changeMessage() {
    const messages = [
        "Keep learning every day!",
        "Small progress is still progress.",
        "Believe in yourself.",
        "Success starts with one step.",
        "Grow as you go!",
        `<a href="https://web.facebook.com/share/v/1BdVYZrRoF/">link</a>`
    ];

    const random = Math.floor(Math.random() * messages.length);

    document.getElementById("message").innerHTML = messages[random];
}
