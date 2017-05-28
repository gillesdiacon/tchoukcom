import { NgModule }             from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { ConditionsComponent }   from './conditions.component';
import { ContactComponent }      from './contact.component';
import { HeroDetailComponent }  from './hero-detail.component';

const routes: Routes = [
  { path: '', redirectTo: '/shop', pathMatch: 'full' },
  { path: 'conditions',  component: ConditionsComponent },
  { path: 'contact',     component: ContactComponent }
  { path: 'detail/:id', component: HeroDetailComponent },
];

@NgModule({
  imports: [ RouterModule.forRoot(routes) ],
  exports: [ RouterModule ]
})

export class AppRoutingModule {}
