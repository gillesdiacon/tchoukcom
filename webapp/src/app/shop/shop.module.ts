import { NgModule }      from '@angular/core';
import { CommonModule }  from '@angular/common';
import { FormsModule }   from '@angular/forms';
import { HttpModule }    from '@angular/http';
import { RouterModule }  from '@angular/router';

import { ShopRoutingModule }     from './shop-routing.module';

import { ShopComponent }          from './shop.component';
import { CategoryMenuComponent }  from './category-menu.component';
import { AddressComponent }       from './address.component';

@NgModule({
    imports: [ 
        CommonModule,
        FormsModule,
        HttpModule,
        ShopRoutingModule
    ],
    declarations: [ ShopComponent, CategoryMenuComponent, AddressComponent ],
    providers:    [  ]
})
export class ShopModule { }
