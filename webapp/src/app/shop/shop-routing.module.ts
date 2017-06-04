import { NgModule }             from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { ShopComponent }        from './shop.component';
import { ShopListComponent }    from './shop-list.component';

const routes: Routes = [
    { 
        path: '',
        component: ShopComponent,
        children: [
            { path: '', redirectTo: 'category/10', pathMatch: 'full' },
            { path: 'category/:id', component: ShopListComponent }
        ]
    }
];

@NgModule({
  imports: [ RouterModule.forChild(routes) ],
  exports: [ RouterModule ]
})

export class ShopRoutingModule {}
