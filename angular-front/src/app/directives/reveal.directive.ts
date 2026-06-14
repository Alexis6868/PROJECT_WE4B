import { Directive, ElementRef, Input, OnDestroy, OnInit } from '@angular/core';

@Directive({
  selector: '[reveal]',
  standalone: true,
})
export class RevealDirective implements OnInit, OnDestroy {
  @Input() revealDelay: number = 0;
  @Input() revealFrom: 'bottom' | 'left' | 'right' = 'bottom';

  private observer!: IntersectionObserver;

  constructor(private el: ElementRef<HTMLElement>) {}

  ngOnInit() {
    const el = this.el.nativeElement;
    const translateMap: Record<string, string> = {
      bottom: 'translateY(32px)',
      left:   'translateX(-32px)',
      right:  'translateX(32px)',
    };
    el.style.opacity = '0';
    el.style.transform = translateMap[this.revealFrom];
    el.style.transition = `opacity .65s cubic-bezier(.22,1,.36,1) ${this.revealDelay}ms, transform .65s cubic-bezier(.22,1,.36,1) ${this.revealDelay}ms`;

    this.observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          el.style.opacity = '1';
          el.style.transform = 'none';
          this.observer.disconnect();
        }
      },
      { threshold: 0.12 }
    );
    this.observer.observe(el);
  }

  ngOnDestroy() {
    this.observer?.disconnect();
  }
}
