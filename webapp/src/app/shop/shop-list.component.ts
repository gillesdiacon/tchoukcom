import 'rxjs/add/operator/switchMap';

import { Component, OnInit }        from '@angular/core';
import { ActivatedRoute, Params }   from '@angular/router';
import { Location }                 from '@angular/common';

import { Category }          from './category';
import { Product }           from './product';
import { ShopService }       from './shop.service';

@Component({
    templateUrl: './shop-list.component.html',
    styleUrls: [ './shop-list.component.css' ]
})

export class ShopListComponent implements OnInit {

    categoryElements: Category[];
    productElements: Product[];

    constructor(
        private shopService: ShopService,
        private route: ActivatedRoute,
        private location: Location) {
        
    }
    
    ngOnInit(): void {
        this.route.params
            .switchMap((params: Params) => this.shopService.getCategory(+params['id']))
            .subscribe(category => {
                
                this.shopService.selectedCategory = category;
                
                if(category.sub_categories && category.sub_categories.length > 0){
                    this.categoryElements = category.sub_categories;
                    this.productElements = null;
                }else{
                    this.categoryElements = null;
                    this.shopService.getProducts(category.id).then(products => this.productElements = products);
                }
            }
        );
    }
}