import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DadosacessoComponent } from './dadosacesso.component';

describe('DadosacessoComponent', () => {
  let component: DadosacessoComponent;
  let fixture: ComponentFixture<DadosacessoComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DadosacessoComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DadosacessoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
