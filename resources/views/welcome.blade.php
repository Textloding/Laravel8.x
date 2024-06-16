<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Service</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            font-family: 'Roboto', sans-serif;
            background: #000;
            color: #fff;
        }
        .info {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 1.5rem;
            opacity: 0.9;
            text-align: center;
        }
        .btn {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            padding: 1rem 2rem;
            font-size: 1.25rem;
            background: linear-gradient(45deg, #00ff00, #0000ff);
            color: #ffffff;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }
        .btn:hover {
            background: linear-gradient(45deg, #0000ff, #00ff00);
            transform: translateX(-50%) scale(1.1);
        }
        canvas {
            display: block;
        }
        .description {
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            font-size: 2rem;
            opacity: 0.8;
        }
        .description p {
            margin: 10px 0;
            color: white;
            text-shadow: 0 0 5px rgba(0, 255, 255, 0.5);
        }
    </style>
</head>
<body>
<div class="description">
    <p id="text1">未 来 已 来</p>
    <p id="text2">Talk With Our Cutting-Edge AI</p>
</div>
<div class="info">移动并点击拖动鼠标与页面进行交互</div>
<button class="btn" onclick="window.location.href='{{ route('chat.index') }}'">开 始 问 答</button>
<canvas id="three-canvas"></canvas>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('three-canvas');
        const renderer = new THREE.WebGLRenderer({ canvas });
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.body.appendChild(renderer.domElement);

        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        camera.position.z = 30;

        const geometry = new THREE.BoxGeometry(5, 5, 5);
        const material = new THREE.MeshStandardMaterial({ color: 0x0077ff });
        const cube = new THREE.Mesh(geometry, material);
        scene.add(cube);

        const light = new THREE.PointLight(0xffffff, 1, 100);
        light.position.set(10, 10, 10);
        scene.add(light);

        let scrollY = 0;
        let mouseX = 0, mouseY = 0;

        function animate() {
            requestAnimationFrame(animate);
            cube.rotation.x += 0.01;
            cube.rotation.y += 0.01;

            camera.position.z = 30 + scrollY * 0.1;
            camera.position.x += (mouseX - camera.position.x) * 0.05;
            camera.position.y += (-mouseY - camera.position.y) * 0.05;

            renderer.render(scene, camera);
        }
        animate();

        document.addEventListener('scroll', () => {
            scrollY = window.scrollY;
        });

        document.addEventListener('mousemove', (event) => {
            mouseX = (event.clientX - window.innerWidth / 2) / 50;
            mouseY = (event.clientY - window.innerHeight / 2) / 50;
        });

        document.addEventListener('click', () => {
            const smokeGeometry = new THREE.PlaneGeometry(10, 10);
            const smokeMaterial = new THREE.MeshBasicMaterial({ color: 0xffffff, side: THREE.DoubleSide, opacity: 0.5, transparent: true });
            const smoke = new THREE.Mesh(smokeGeometry, smokeMaterial);
            smoke.position.set(camera.position.x, camera.position.y, camera.position.z - 10);
            scene.add(smoke);

            gsap.to(smoke.material, {
                opacity: 0,
                duration: 1,
                onComplete: () => scene.remove(smoke)
            });
        });

        window.addEventListener('resize', () => {
            renderer.setSize(window.innerWidth, window.innerHeight);
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
        });

        // Text animation
        const colors = ["#ff0000", "#00ff00", "#0000ff", "#ffff00", "#ff00ff", "#00ffff"];
        const timeline = gsap.timeline({ repeat: -1, yoyo: true });
        timeline.to("#text1", { duration: 2, color: colors[0], ease: "none" })
            .to("#text1", { duration: 2, color: colors[1], ease: "none" })
            .to("#text1", { duration: 2, color: colors[2], ease: "none" })
            .to("#text1", { duration: 2, color: colors[3], ease: "none" })
            .to("#text1", { duration: 2, color: colors[4], ease: "none" })
            .to("#text1", { duration: 2, color: colors[5], ease: "none" });

        const timeline2 = gsap.timeline({ repeat: -1, yoyo: true });
        timeline2.to("#text2", { duration: 2, color: colors[0], ease: "none" })
            .to("#text2", { duration: 2, color: colors[1], ease: "none" })
            .to("#text2", { duration: 2, color: colors[2], ease: "none" })
            .to("#text2", { duration: 2, color: colors[3], ease: "none" })
            .to("#text2", { duration: 2, color: colors[4], ease: "none" })
            .to("#text2", { duration: 2, color: colors[5], ease: "none" });
    });
</script>
</body>
</html>
