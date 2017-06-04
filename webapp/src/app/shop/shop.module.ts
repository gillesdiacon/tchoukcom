import { NgModule }      from '@angular/core';
import { CommonModule }  from '@angular/common';
import { FormsModule }   from '@angular/forms';
import { HttpModule }    from '@angular/http';
import { RouterModule }  from '@angular/router';

import { ShopRoutingModule }     from './shop-routing.module';

import { ShopComponent }          from './shop.component';
import { ShopListComponent }      from './shop-list.component';
import { CategoryMenuComponent }  from './category-menu.component';
import { AddressComponent }       from './address.component';

@NgModule({
    imports: [ 
        CommonModule,
        FormsModule,
        HttpModule,
        ShopRoutingModule
    ],
    declarations: [ ShopComponent, ShopListComponent, CategoryMenuComponent, AddressComponent ],
    providers:    [  ],
    bootstrap:    [ ShopComponent ]
})
export class ShopModule { }
