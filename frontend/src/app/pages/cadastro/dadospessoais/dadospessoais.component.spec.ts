import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DadospessoaisComponent } from './dadospessoais.component';

describe('DadospessoaisComponent', () => {
  let component: DadospessoaisComponent;
  let fixture: ComponentFixture<DadospessoaisComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DadospessoaisComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DadospessoaisComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
