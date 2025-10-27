<?php
namespace App\Services\Admin\Blog;

use App\Models\Api\Admin\Blog;
use App\Services\BaseModelService;
use App\Traits\HandlesImage;
use App\Traits\StoreMultiLang;
use Illuminate\Database\Eloquent\Builder;
class BlogService extends BaseModelService{
    
    use StoreMultiLang , HandlesImage;
    protected string $modelClass = Blog::class;
    protected array  $relations  = ['category'];




    public function all($request){
        $blog = parent::all($request);
        return $blog;
    }

    public function view($id){
        $blogDetails = parent::view($id);
        return $blogDetails;
    }

    public function store()
    {
  
        $this->uploadSingleImage(['blog_image' , 'breadcrumb'] , 'uploads/blog');
        $this->data['slug']  = $this->createSlug($this->data);
        $blog = parent::store($this->getBasicColumn(['breadcrumb' , 'image','category_id','is_active']));
        $this->processTranslations($blog, $this->data, ['title', 'slug' ,'des' , 'small_des' , 'meta_title' , 'meta_des', 'alt_image' , 'title_image']);  
        return $blog;
        
    }
    


    public function update($id ){
        $this->uploadSingleImage(['blog_image' , 'breadcrumb'] , 'uploads/blog');
        $blog = parent::update($id , $this->getBasicColumn(['breadcrumb' , 'image','category_id','is_active']));
        $this->processTranslations($blog, $this->data, ['title', 'slug' ,'des' , 'small_des' , 'meta_title' , 'meta_des', 'alt_image' , 'title_image']);
        return $blog;
        
    }

    public function delete($id){
        $blog = parent::delete($id);
        return $blog;
    }


    public function applySearch(Builder $query, string $search ){
        return $query->where(function ($q) use ($search) {
            $q->whereTranslationLike('title', "%$search%")
              ->orWhereTranslationLike('slug', "%$search%");
        });
    }

    public function orderBy(Builder $query, string $orderBy, string $direction = 'asc')
    {
        return $query->orderBy($orderBy, $direction);
    }


    


}