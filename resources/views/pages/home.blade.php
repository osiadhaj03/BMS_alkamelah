@extends('layouts.app')

@section('seo_title', 'المكتبة الكاملة | أكبر مكتبة رقمية للبحث في كتب التراث')
@section('seo_description', 'المكتبة الكاملة: محرك بحث ذكي في آلاف أمهات الكتب العربية. اكتشف كتب الفقه، التاريخ، الأدب، والأنساب بسهولة وسرعة.')

@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "المكتبة الكاملة",
  "alternateName": "Al-Kamelah Library",
  "url": "https://alkamelah.com/",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "https://alkamelah.com/search?q={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [{
    "@type": "ListItem",
    "position": 1,
    "name": "المكتبة الكاملة",
    "item": "https://alkamelah.com/"
  }]
}
</script>
@endpush

@section('content')
    <!-- Header -->
    @include('components.layout.header')

    <!-- Hero Section -->
    @include('components.layout.hero')

    @include('components.layout.category')

    <!-- Books Section -->
    @include('components.layout.books-section')

    <!-- Authors Section -->
    @include('components.layout.authors-section')

    <!-- Footer -->
    @include('components.layout.footer')
@endsection