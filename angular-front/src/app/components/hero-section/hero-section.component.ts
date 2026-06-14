import {
  Component, AfterViewInit, OnDestroy,
  ElementRef, ViewChild, HostListener,
} from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import * as THREE from 'three';
import { gsap } from 'gsap';

interface StatCard { label: string; value: string; desc: string; }

@Component({
  selector: 'app-hero-section',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './hero-section.component.html',
  styleUrl: './hero-section.component.css',
})
export class HeroSectionComponent implements AfterViewInit, OnDestroy {
  @ViewChild('heroCanvas') canvasRef!: ElementRef<HTMLCanvasElement>;

  webglFailed = false;
  isMobile    = typeof window !== 'undefined' && window.innerWidth < 768;
  reducedMotion = typeof window !== 'undefined' &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  stats: StatCard[] = [
    { label: 'Vitesse max',    value: '72 km/h',  desc: 'Sur route pavée'    },
    { label: 'Blindage',       value: '500 mm',   desc: 'Équivalent RHA'     },
    { label: 'Autonomie',      value: '550 km',   desc: 'Charge combat full' },
    { label: 'Disponibilité',  value: '24 / 7',   desc: 'Livraison mondiale' },
  ];

  private renderer!: THREE.WebGLRenderer;
  private scene!: THREE.Scene;
  private camera!: THREE.PerspectiveCamera;
  private tank?: THREE.Group;
  private particles!: THREE.Points;
  private frameId!: number;
  private targetCamX = 0;
  private targetCamY = 0.4;

  // ─── Lifecycle ────────────────────────────────────────────────────────────

  ngAfterViewInit(): void {
    if (this.isMobile) { this.animateUI(); return; }
    if (!this.isWebGLAvailable()) { this.webglFailed = true; this.animateUI(); return; }
    this.initThree();
    this.animateUI();
  }

  ngOnDestroy(): void {
    cancelAnimationFrame(this.frameId);
    this.renderer?.dispose();
  }

  // ─── Events ───────────────────────────────────────────────────────────────

  @HostListener('mousemove', ['$event'])
  onMouseMove(e: MouseEvent): void {
    if (this.isMobile || !this.renderer) return;
    const nx = (e.clientX / window.innerWidth  - 0.5) * 2;
    const ny = (e.clientY / window.innerHeight - 0.5) * 2;
    this.targetCamX =  nx * 1.4;
    this.targetCamY =  0.4 - ny * 0.7;
  }

  @HostListener('window:resize')
  onResize(): void {
    if (!this.renderer) return;
    const w = window.innerWidth, h = window.innerHeight;
    this.camera.aspect = w / h;
    this.camera.updateProjectionMatrix();
    this.renderer.setSize(w, h);
  }

  // ─── Three.js init ────────────────────────────────────────────────────────

  private isWebGLAvailable(): boolean {
    try {
      const c = document.createElement('canvas');
      return !!(window.WebGLRenderingContext &&
        (c.getContext('webgl') || c.getContext('experimental-webgl')));
    } catch { return false; }
  }

  private async initThree(): Promise<void> {
    const w = window.innerWidth, h = window.innerHeight;
    const canvas = this.canvasRef.nativeElement;

    // Renderer
    this.renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
    this.renderer.setSize(w, h);
    this.renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
    this.renderer.shadowMap.enabled = true;
    this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
    this.renderer.toneMappingExposure = 1.7;

    // Scene + fog — slightly lighter deep blue-dark
    this.scene = new THREE.Scene();
    this.scene.background = new THREE.Color(0x10101e);
    this.scene.fog = new THREE.Fog(0x10101e, 20, 42);

    // Camera — starts far for GSAP zoom
    this.camera = new THREE.PerspectiveCamera(44, w / h, 0.1, 100);
    this.camera.position.set(0, 1.5, this.reducedMotion ? 7.5 : 14);
    this.camera.lookAt(0, 0.35, 0);

    this.setupLights();
    this.scene.add(this.buildGround());
    this.particles = this.buildParticles();
    this.scene.add(this.particles);

    // Start the render loop immediately (tank will appear when loaded)
    this.startLoop();

    // Load AMX-56 USDZ model; fall back to procedural tank on failure
    await this.loadTankModel();

    // GSAP camera zoom-in after model is ready
    if (!this.reducedMotion) {
      gsap.to(this.camera.position, {
        z: 7.5, duration: 2.8, ease: 'power3.out',
        onUpdate: () => this.camera.lookAt(0, 0.35, 0),
      });
    }
  }

  private async loadTankModel(): Promise<void> {
    try {
      const { GLTFLoader } = await import('three/examples/jsm/loaders/GLTFLoader.js');

const loader = new GLTFLoader();

const gltf = await loader.loadAsync('assets/amx_56_lowpoly.glb');

// le modèle 3D est dans gltf.scene
const group = gltf.scene;

      // Apply dark metallic material to all meshes
      const darkMat = new THREE.MeshStandardMaterial({
        color: 0x1e2232, metalness: 0.82, roughness: 0.28,
      });
      const accentMat = new THREE.MeshStandardMaterial({
        color: 0xff7a00, metalness: 0.9, roughness: 0.12,
        emissive: new THREE.Color(0xff4400), emissiveIntensity: 0.4,
      });
      let meshIndex = 0;
      group.traverse((obj: THREE.Object3D) => {
        if (obj instanceof THREE.Mesh) {
          obj.material = meshIndex % 8 === 0 ? accentMat : darkMat;
          obj.castShadow = true;
          obj.receiveShadow = true;
          meshIndex++;
        }
      });

      // Auto-scale to fit scene (~4 units wide)
      const box = new THREE.Box3().setFromObject(group);
      const size = box.getSize(new THREE.Vector3());
      const maxDim = Math.max(size.x, size.y, size.z);
      if (maxDim > 0) group.scale.setScalar(4 / maxDim);

      // Place on ground
      const box2 = new THREE.Box3().setFromObject(group);
      group.position.y = -box2.min.y - 0.34;
      group.rotation.y = Math.PI / 7;

      this.tank = group as unknown as THREE.Group;
      this.scene.add(this.tank);
    } catch (e) {
      console.warn('USDZ load failed, using procedural tank:', e);
      this.tank = this.buildTank();
      this.scene.add(this.tank);
    }
  }

  private setupLights(): void {
    // Bright ambient with a warm tint
    this.scene.add(new THREE.AmbientLight(0x1a2040, 10));

    // Key — warm orange-white, upper-left front
    const key = new THREE.DirectionalLight(0xffcc88, 5.5);
    key.position.set(-4, 8, 4);
    key.castShadow = true;
    key.shadow.mapSize.set(1024, 1024);
    this.scene.add(key);

    // Rim — cool blue from upper-right behind
    const rim = new THREE.DirectionalLight(0x4477ff, 4.5);
    rim.position.set(6, 3, -7);
    this.scene.add(rim);

    // Fill — soft front light
    const fill = new THREE.DirectionalLight(0xffffff, 1.5);
    fill.position.set(0, 4, 8);
    this.scene.add(fill);

    // Orange under-glow (ground bounce)
    const bounce = new THREE.PointLight(0xff7a00, 3.5, 12);
    bounce.position.set(0, -0.15, 2);
    this.scene.add(bounce);
  }

  // ─── Procedural tank (fallback) ───────────────────────────────────────────

  private buildTank(): THREE.Group {
    const tank = new THREE.Group();

    const dark = new THREE.MeshStandardMaterial({
      color: 0x1a1f2e, metalness: 0.8, roughness: 0.3,
    });
    const accent = new THREE.MeshStandardMaterial({
      color: 0xff7a00, metalness: 0.9, roughness: 0.1,
      emissive: new THREE.Color(0xff4400), emissiveIntensity: 0.45,
    });
    const wireMat = new THREE.LineBasicMaterial({
      color: 0xff7a00, transparent: true, opacity: 0.22,
    });

    const mesh = (geo: THREE.BufferGeometry, mat: THREE.Material, sx = 0, sy = 0, sz = 0, rx = 0, ry = 0, rz = 0) => {
      const m = new THREE.Mesh(geo, mat);
      m.position.set(sx, sy, sz);
      m.rotation.set(rx, ry, rz);
      m.castShadow = m.receiveShadow = true;
      tank.add(m);
      return m;
    };

    // Hull
    const hullGeo = new THREE.BoxGeometry(3.7, 0.65, 1.85);
    mesh(hullGeo, dark, 0, 0.1, 0);
    const wire = new THREE.LineSegments(new THREE.EdgesGeometry(hullGeo), wireMat);
    wire.position.set(0, 0.1, 0);
    tank.add(wire);

    // Front slope
    mesh(new THREE.BoxGeometry(0.72, 0.64, 1.85), dark, 2.14, 0.1, 0, 0, 0, 0.28);
    mesh(new THREE.BoxGeometry(0.9, 0.14, 1.6), dark, -1.55, 0.49, 0);

    // Track fenders
    for (const z of [-1.1, 1.1]) {
      mesh(new THREE.BoxGeometry(4.1, 0.3, 0.25), dark, 0, 0, z);
    }

    // Turret
    const turretGeo = new THREE.BoxGeometry(1.55, 0.58, 1.15);
    mesh(turretGeo, dark, 0.15, 0.65, 0);
    mesh(new THREE.BoxGeometry(1.55, 0.04, 1.15), accent, 0.15, 0.945, 0);
    const tWire = new THREE.LineSegments(new THREE.EdgesGeometry(turretGeo), wireMat);
    tWire.position.set(0.15, 0.65, 0);
    tank.add(tWire);

    mesh(new THREE.BoxGeometry(0.3, 0.45, 0.55), dark, 0.95, 0.72, 0);
    mesh(new THREE.CylinderGeometry(0.055, 0.072, 3.1, 8), dark, 1.9, 0.72, 0, 0, 0, Math.PI / 2);
    mesh(new THREE.CylinderGeometry(0.1, 0.1, 0.25, 8), dark, 3.4, 0.72, 0, 0, 0, Math.PI / 2);

    for (const [x, z] of [[-1.65, -0.5], [-1.45, -0.65]]) {
      mesh(new THREE.CylinderGeometry(0.055, 0.055, 0.4, 6), dark, x, 0.62, z);
    }
    mesh(new THREE.CylinderGeometry(0.22, 0.22, 0.08, 12), dark, 0.1, 0.97, 0);

    tank.rotation.y = Math.PI / 7;
    return tank;
  }

  private buildGround(): THREE.Mesh {
    const m = new THREE.Mesh(
      new THREE.PlaneGeometry(30, 30),
      new THREE.MeshStandardMaterial({ color: 0x0d0f1c, metalness: 0.55, roughness: 0.48 }),
    );
    m.rotation.x = -Math.PI / 2;
    m.position.y = -0.34;
    m.receiveShadow = true;
    return m;
  }

  private buildParticles(): THREE.Points {
    const count = 200;
    const pos = new Float32Array(count * 3);
    for (let i = 0; i < count * 3; i += 3) {
      pos[i]     = (Math.random() - 0.5) * 18;
      pos[i + 1] = (Math.random() - 0.5) * 9;
      pos[i + 2] = (Math.random() - 0.5) * 12;
    }
    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
    return new THREE.Points(geo, new THREE.PointsMaterial({
      color: 0xff7a00, size: 0.032, transparent: true, opacity: 0.45,
    }));
  }

  // ─── Animation loop ───────────────────────────────────────────────────────

  private startLoop(): void {
    const attr = this.particles.geometry.attributes['position'] as THREE.BufferAttribute;
    const arr  = attr.array as Float32Array;

    const tick = () => {
      this.frameId = requestAnimationFrame(tick);

      if (this.tank) this.tank.rotation.y += 0.0022;

      for (let i = 1; i < arr.length; i += 3) {
        arr[i] += 0.004;
        if (arr[i] > 4.5) arr[i] = -4.5;
      }
      attr.needsUpdate = true;

      this.camera.position.x += (this.targetCamX - this.camera.position.x) * 0.035;
      this.camera.position.y += (this.targetCamY - this.camera.position.y) * 0.035;
      this.camera.lookAt(0, 0.35, 0);

      this.renderer.render(this.scene, this.camera);
    };
    tick();
  }

  // ─── UI entrance animations ───────────────────────────────────────────────

  private animateUI(): void {
    if (this.reducedMotion) return;
    gsap.timeline({ delay: 0.2 })
      .from('.hs-tagline', { opacity: 0, x: -44, duration: 1.0, ease: 'power3.out' })
      .from('.hs-sub',     { opacity: 0, x: -22, duration: 0.75, ease: 'power2.out' }, '-=0.55')
      .from('.hs-cta',     { opacity: 0, y: 22,  duration: 0.6,  ease: 'back.out(1.7)' }, '-=0.4')
      .from('.hs-card',    { opacity: 0, x: 44, y: 12, duration: 0.65, stagger: 0.1, ease: 'power3.out' }, '-=0.85');
  }
}
