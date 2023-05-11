import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BarraAcoesComponent } from './barra-acoes.component';

describe('BarraAcoesComponent', () => {
  let component: BarraAcoesComponent;
  let fixture: ComponentFixture<BarraAcoesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ BarraAcoesComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(BarraAcoesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
