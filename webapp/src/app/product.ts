import { ProductI18n }          from './product-i18n';

export class Product {
    id: number;
    code: string;
    category_id: number;
    variant_group_id: number;
    title: ProductI18n;
}