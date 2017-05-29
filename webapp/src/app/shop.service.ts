import { Injectable } from '@angular/core';
import { Headers, Http } from '@angular/http';

import 'rxjs/add/operator/toPromise';

import { Category } from './category';
import { Product } from './product';

@Injectable()
export class ShopService {

    constructor(private http: Http) { }

    getCategories(): Promise<Category[]> {
        return this.http.get('http://localhost/tchoukcom/backend/v1/public/api/shopcategories')
            .toPromise()
            .then(response => response.json() as Category[])
            .catch(this.handleError);
    }
    
    getProducts($categoryId: number): Promise<Product[]> {
        console.log($categoryId);
        return this.http.get('http://localhost/tchoukcom/backend/v1/public/api/shopproducts/' + $categoryId)
            .toPromise()
            .then(response => response.json() as Product[])
            .catch(this.handleError);
    }
    
    private handleError(error: any): Promise<any> {
        console.error('An error occurred', error);
        return Promise.reject(error.message || error);
    }
}