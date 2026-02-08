# UltraFastSearchService Comparison

Files:
- app/Services/UltraFastSearchService.php (current project)
- C:\Users\osaid\Documents\BMS_alkamelah\BMS_v1-homev2\app\Services\UltraFastSearchService.php (old project)

Summary of Differences (Current vs Old)
- Added search types: fuzzy, prefix, wildcard, boolean.
- Added query-level timeout and terminate_after; added request timeout in body.
- Added dynamic sorting via sort_by with buildSort; old uses fixed _score sorting.
- Added support for word_match filter and related logic in flexible/morphological queries.
- Added extra highlighting field content.advanced and highlighted_content output.
- Added matched_terms extraction and inclusion in results.
- Added book metadata enrichment in results (description, publisher, total_pages, author_name from DB).
- Author filter now applied via author_ids terms (old logs warning and skips author filter).
- Index selection strategy changed:
  - Current: uses pages_active alias and verifies it exists, logs connection issues.
  - Old: scans a list of indices (pages_new_search, pages, pages_test, pages_optimized).
- buildExactMatchQuery, buildFlexibleMatchQuery, buildMorphologicalQuery logic changed.
  - Current: uses stricter match_phrase on content, simple_query_string, and wildcard-based morphology for any_order.
  - Old: uses content.flexible/content.stemmed match/match_phrase with operator=and.
- getActiveIndex changed:
  - Current: only checks pages.
  - Old: scans multiple indices.

Behavioral Impact Notes
- Query behavior can differ significantly due to analyzer/field changes (content vs content.flexible/content.stemmed, wildcard use).
- Author filtering now affects results (requires author_ids indexed).
- Sorting can be configured (relevance, least_relevance, death_year_asc/desc).
- Result payload expanded with more book metadata and matched terms; API consumers may need to handle new fields.

How to Reproduce Full Diff
- Run from repo root:
  git diff --no-index -- app/Services/UltraFastSearchService.php "C:\Users\osaid\Documents\BMS_alkamelah\BMS_v1-homev2\app\Services\UltraFastSearchService.php"
