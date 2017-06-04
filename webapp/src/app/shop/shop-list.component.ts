import 'rxjs/add/operator/switchMap';

import { Component, OnInit }        from '@angular/core';
import { ActivatedRoute, Params }   from '@angular/router';
import { Location }                 from '@angular/common';

import { Category }          from './category';
import { Product }           from './product';
import { ShopService }       from './shop.service';

@Component({
    templateUrl: './shop-list.component.html',
    styleUrls: [ './shop-list.component.css' ],
    providers: [ ShopService ]
})

export class ShopListComponent implements OnInit {

    // in the element (center column)
    categoryElements: Category[];
    productElements: Product[];

    constructor(
        private shopService: ShopService,
        private route: ActivatedRoute,
        private location: Location) {
        
    }
    
    ngOnInit(): void {
        // this.route.params.switchMap((params: Params) => this.heroService.getHero(+params['id'])).subscribe(hero => this.hero = hero);
        // this.route.params.switchMap((params: Params) => console.log(+params['id']));

        this.route.params.switchMap((params: Params) => this.shopService.getCategories(+params['id'])).subscribe(categories => this.categoryElements = categories);
    }

    // // for category menu
    // categories: Category[];
    // selectedCategory: Category;
    

    // constructor(private shopService: ShopService, private router: Router) {
    // }

    // ngOnInit(): void {
        // this.shopService
            // .getCategories(10)
            // .then(categories => this.categories = this.categoryElements = categories);
    // }
    
    // onSelect(category: Category): void {
        // this.selectedCategory = category;
        // if(category.sub_categories){
            // this.categoryElements = category.sub_categories;
            // this.productElements = null;
        // }else{
            // this.categoryElements = null;
            // this.shopService.getProducts(category.id).then(products => this.productElements = products);
        // }
    // }
}