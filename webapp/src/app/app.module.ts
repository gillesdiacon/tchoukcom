import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule }   from '@angular/forms';
import { HttpModule }    from '@angular/http';
import { RouterModule }   from '@angular/router';

import { AppRoutingModule }     from './app-routing.module';

import { AppComponent }  from './app.component';
import { ShopComponent } from './shop.component';
import { ConditionsComponent } from './conditions.component';
import { ContactComponent } from './contact.component';
import { AddressComponent } from './address.component';
import { HeroDetailComponent } from './hero-detail.component';

import { HeroService } from './hero.service';

@NgModule({
    imports: [ 
        BrowserModule,
        FormsModule,
        HttpModule,
        AppRoutingModule
    ],
    declarations: [ AppComponent, ShopComponent, ConditionsComponent, ContactComponent, AddressComponent, HeroDetailComponent ],
    providers:    [ HeroService ],
    bootstrap:    [ AppComponent ]
})
export class AppModule { }
