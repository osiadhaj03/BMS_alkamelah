<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookReaderController extends Controller
{
    public function show(Book $book, Request $request)
    {
        // Get current page from request, default to 1
        $currentPage = $request->get('page', 1);
        
        // Static sections for now (no database dependency)
        $sections = collect([
            (object) ['id' => 1, 'title' => 'آداب الفتوى والمفتي', 'start_page' => 1, 'end_page' => 15],
            (object) ['id' => 2, 'title' => 'شروط المفتي وأحكامه', 'start_page' => 16, 'end_page' => 30],
            (object) ['id' => 3, 'title' => 'آداب المستفتي', 'start_page' => 31, 'end_page' => 45],
            (object) ['id' => 4, 'title' => 'أصول الإفتاء والاجتهاد', 'start_page' => 46, 'end_page' => 60],
            (object) ['id' => 5, 'title' => 'قواعد الفتوى والترجيح', 'start_page' => 61, 'end_page' => 75],
            (object) ['id' => 6, 'title' => 'النوازل والمستجدات', 'start_page' => 76, 'end_page' => 90],
            (object) ['id' => 7, 'title' => 'مناهج الأئمة في الفتوى', 'start_page' => 91, 'end_page' => 105],
            (object) ['id' => 8, 'title' => 'ضوابط الفتوى المعاصرة', 'start_page' => 106, 'end_page' => 120],
        ]);
        
        // Static data
        $totalPages = 120;
        
        // Calculate navigation
        $previousPage = $currentPage > 1 ? $currentPage - 1 : null;
        $nextPage = $currentPage < $totalPages ? $currentPage + 1 : null;
        
        // Sample content for demonstration
        $bookContent = $this->getBookContent($book, $currentPage);
        
        // Add static book info
        $book->author = (object) ['name' => 'الإمام النووي'];
        $book->section = (object) ['name' => 'أصول الفقه والقواعد الفقهية'];
        
        return view('pages.book-reader', compact(
            'book', 
            'sections', 
            'currentPage', 
            'totalPages', 
            'previousPage', 
            'nextPage',
            'bookContent'
        ));
    }
    
    private function getBookContent($book, $page)
    {
        // Static content based on page number
        $content = [
            1 => "بسم الله الرحمن الرحيم\n\nالحمد لله رب العالمين، والصلاة والسلام على أشرف الأنبياء والمرسلين، نبينا محمد وعلى آله وصحبه أجمعين.\n\nأما بعد: فإن من أعظم ما يحتاج إليه طالب العلم معرفة آداب الفتوى والمفتي والمستفتي، وذلك لأن الفتوى أمر عظيم، وشأن خطير، إذ هي توقيع عن الله تبارك وتعالى.\n\nوقد اعتنى العلماء بهذا الجانب عناية فائقة، وألفوا في آداب الفتوى مؤلفات كثيرة، منها ما هو مستقل، ومنها ما هو ضمن مؤلفات أخرى في أصول الفقه أو غيرها.",
            
            2 => "الباب الأول: في آداب المفتي\n\nينبغي للمفتي أن يتحلى بآداب عديدة، منها ما يتعلق بعلمه وفقهه، ومنها ما يتعلق بأخلاقه وسلوكه، ومنها ما يتعلق بطريقة إفتائه وأسلوبه في التعامل مع المستفتين.\n\nفمن الآداب المتعلقة بالعلم: أن يكون عالماً بأحكام الشريعة، عارفاً بأدلتها من الكتاب والسنة والإجماع والقياس، متمكناً من فهم النصوص وتطبيقها على الوقائع والنوازل.\n\nومن الآداب المتعلقة بالأخلاق: أن يكون ورعاً تقياً، خائفاً من الله تعالى، حريصاً على الحق، متواضعاً غير متكبر، صبوراً حليماً، لا يغضب إذا خولف في رأيه.",
            
            3 => "الفصل الأول: شروط المفتي\n\nيشترط في المفتي شروط عديدة، وهي:\n\n1. الإسلام: فلا يجوز للكافر أن يفتي في أحكام الإسلام.\n\n2. التكليف: فلا يجوز للصبي والمجنون أن يفتيا.\n\n3. العدالة: فلا يجوز للفاسق أن يفتي، لأن فتواه لا يوثق بها.\n\n4. العلم: وهو الشرط الأهم، ويتضمن:\n   - العلم بالكتاب والسنة\n   - العلم بأصول الفقه\n   - العلم بمقاصد الشريعة\n   - العلم باللغة العربية\n   - العلم بالناسخ والمنسوخ",
        ];
        
        // Return content for specific page or default content
        return $content[$page] ?? "محتوى الصفحة {$page}\n\nهذا نص تجريبي للصفحة رقم {$page} من كتاب {$book->title}. يحتوي هذا النص على محتوى عام حول آداب الفتوى والمفتي والمستفتي.\n\nيمكن تخصيص هذا المحتوى لكل صفحة بحيث يعكس المحتوى الحقيقي للكتاب. هذا مجرد نموذج للعرض والتصميم.\n\nالنص هنا باللغة العربية ويستخدم خط Tajawal للحصول على أفضل عرض للنصوص العربية في المتصفح.";
    }
}