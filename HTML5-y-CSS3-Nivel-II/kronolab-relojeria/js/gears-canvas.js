const canvas = document.getElementById('gearsCanvas');
const ctx = canvas.getContext('2d');
const rpmRange = document.getElementById('rpmRange');
const rpmOut = document.getElementById('rpmOut');
const reverseBtn = document.getElementById('reverseBtn');

let angle = 0;
let speed = 1;
let direction = 1;
let sparks = [];

function drawGear(cx, cy, outerRadius, innerRadius, teeth, rotation, color) {
  ctx.save();
  ctx.translate(cx, cy);
  ctx.rotate(rotation);

  ctx.beginPath();
  const step = (Math.PI * 2) / teeth;

  for (let i = 0; i < teeth; i++) {
    const a1 = i * step;
    const a2 = a1 + step * 0.25;
    const a3 = a1 + step * 0.5;
    const a4 = a1 + step * 0.75;

    ctx.lineTo(Math.cos(a1) * innerRadius, Math.sin(a1) * innerRadius);
    ctx.lineTo(Math.cos(a2) * outerRadius, Math.sin(a2) * outerRadius);
    ctx.lineTo(Math.cos(a3) * outerRadius, Math.sin(a3) * outerRadius);
    ctx.lineTo(Math.cos(a4) * innerRadius, Math.sin(a4) * innerRadius);
  }

  ctx.closePath();
  ctx.fillStyle = color;
  ctx.fill();
  ctx.lineWidth = 2;
  ctx.strokeStyle = '#f4f1ea';
  ctx.stroke();

  // Eje central hueco
  ctx.beginPath();
  ctx.arc(0, 0, innerRadius * 0.35, 0, Math.PI * 2);
  ctx.fillStyle = '#141c24';
  ctx.fill();
  ctx.stroke();

  ctx.restore();
}

function updateSparks() {
  for (let i = sparks.length - 1; i >= 0; i--) {
    const s = sparks[i];
    s.x += s.vx;
    s.y += s.vy;
    s.life -= 0.03;

    if (s.life <= 0) {
      sparks.splice(i, 1);
      continue;
    }

    ctx.fillStyle = `rgba(212, 160, 23, ${s.life})`;
    ctx.beginPath();
    ctx.arc(s.x, s.y, 2.5, 0, Math.PI * 2);
    ctx.fill();
  }
}

function animate() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  // Líneas de referencia técnica de fondo
  ctx.strokeStyle = 'rgba(255, 255, 255, 0.06)';
  ctx.lineWidth = 1;
  ctx.beginPath();
  ctx.moveTo(0, canvas.height / 2);
  ctx.lineTo(canvas.width, canvas.height / 2);
  ctx.stroke();

  // Engranaje principal (16 dientes) y piñón acoplado (10 dientes)
  drawGear(270, 160, 92, 74, 16, angle, '#b8860b');
  drawGear(408, 160, 62, 46, 10, -angle * (16 / 10) + Math.PI / 6.6, '#55606b');

  updateSparks();

  angle += 0.015 * speed * direction;
  requestAnimationFrame(animate);
}

rpmRange.addEventListener('input', (e) => {
  speed = parseFloat(e.target.value);
  rpmOut.textContent = speed.toFixed(1);
});

reverseBtn.addEventListener('click', () => {
  direction *= -1;
});

canvas.addEventListener('mousemove', (e) => {
  const rect = canvas.getBoundingClientRect();
  const scaleX = canvas.width / rect.width;
  const scaleY = canvas.height / rect.height;
  const mx = (e.clientX - rect.left) * scaleX;
  const my = (e.clientY - rect.top) * scaleY;

  for (let i = 0; i < 3; i++) {
    sparks.push({
      x: mx,
      y: my,
      vx: (Math.random() - 0.5) * 3,
      vy: (Math.random() - 0.5) * 3,
      life: 1
    });
  }
});

animate();
