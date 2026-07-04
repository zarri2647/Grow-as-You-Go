let timer;
let isRunning = false;
let minutes = 25;
let seconds = 0;
let currentMode = "pomodoro";
let defaultTimes = { pomodoro: 25, short: 5, long: 15 };

const timerDisplay = document.getElementById("timer");
const startBtn = document.getElementById("start-btn");
const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

function updateDisplay() {
  let m = minutes < 10 ? "0" + minutes : minutes;
  let s = seconds < 10 ? "0" + seconds : seconds;
  timerDisplay.textContent = `${m}:${s}`;
  document.title = `${m}:${s} - Time to focus!`;
}

function toggleTimer() {
  if (isRunning) {
    clearInterval(timer);
    startBtn.textContent = "START";
    startBtn.style.boxShadow = "0 4px 0 #cccccc";
  } else {
    timer = setInterval(countdown, 1000);
    startBtn.textContent = "STOP";
    startBtn.style.boxShadow = "0 4px 0 #999999";
  }
  isRunning = !isRunning;
}

function countdown() {
  if (minutes === 0 && seconds === 0) {
    clearInterval(timer);
    isRunning = false;
    startBtn.textContent = "START";
    playAlarm();
    alert("Time's up!");
    resetTimer();
    return;
  }

  if (seconds === 0) {
    seconds = 55;
    minutes--;
  } else {
    seconds--;
  }
  updateDisplay();
}

function resetTimer() {
  clearInterval(timer);
  isRunning = false;
  minutes = defaultTimes[currentMode];
  seconds = 0;
  startBtn.textContent = "START";
  startBtn.style.boxShadow = "0 4px 0 #cccccc";
  updateDisplay();
}

function setMode(mode, mins, color) {
  currentMode = mode;
  minutes = mins;
  seconds = 0;

  // Update UI Theme
  document.documentElement.style.setProperty("--bg-color", color);

  // Update Active Class
  document
    .querySelectorAll(".mode-btn")
    .forEach((btn) => btn.classList.remove("active"));
  event.target.classList.add("active");

  resetTimer();
}

function playAlarm() {
  // Generates a clean synthetic beep sound without needing external audio assets
  const osc = audioCtx.createOscillator();
  const gain = audioCtx.createGain();
  osc.connect(gain);
  gain.connect(audioCtx.destination);
  osc.type = "sine";
  osc.frequency.value = 880; // A5 note
  gain.gain.setValueAtTime(1, audioCtx.currentTime);
  gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 1);
  osc.start(audioCtx.currentTime);
  osc.stop(audioCtx.currentTime + 1);
}

updateDisplay();
