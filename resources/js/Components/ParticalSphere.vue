<template>
    <div class="wrap">
        <div v-for="n in total" :key="n" class="c"></div>
    </div>
</template>

<script setup>
const total = 300;
</script>

<style scoped>
html,
body,
.wrap {
    height: 100%;
    margin: 0;
}

body {
    background: black;
    overflow: hidden;
}

.wrap {
    position: relative;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    transform-style: preserve-3d;
    perspective: 1000px;
    animation: rotate 14s infinite linear;
}

@keyframes rotate {
    100% {
        transform: rotateY(360deg) rotateX(360deg);
    }
}

.c {
    position: absolute;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    opacity: 0;
}

/* Динамічно генеруємо правила для 300 частинок */
</style>

<script>
// Тут згенеруємо CSS для кожної частинки через JS
const style = document.createElement('style');
const total = 300;
const orbSize = 300;
const time = 14;
const baseHue = 0;

for (let i = 1; i <= total; i++) {
    const z = Math.random() * 360;
    const y = Math.random() * 360;
    const hue = (40 / total * i) + baseHue;
    const delay = i * 0.01;

    style.innerHTML += `
    .c:nth-child(${i}) {
      animation: orbit${i} ${time}s infinite;
      animation-delay: ${delay}s;
      background-color: hsla(${hue}, 100%, 50%, 1);
    }
  
    @keyframes orbit${i} {
      20% {
        opacity: 1;
      }
      30% {
        transform: rotateZ(-${z}deg) rotateY(${y}deg) translateX(${orbSize}px) rotateZ(${z}deg);
      }
      80% {
        transform: rotateZ(-${z}deg) rotateY(${y}deg) translateX(${orbSize}px) rotateZ(${z}deg);
        opacity: 1;
      }
      100% {
        transform: rotateZ(-${z}deg) rotateY(${y}deg) translateX(${orbSize * 3}px) rotateZ(${z}deg);
      }
    }
    `;
}
document.head.appendChild(style);
</script>