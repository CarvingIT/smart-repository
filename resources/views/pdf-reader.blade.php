<html>
<head>
<script src="/js/pdf.mjs"></script>
<style>
#the-canvas {
  /*border: 1px solid black;*/
  direction: ltr;
  background: url('/i/processing.gif') no-repeat;
  background-position:center;
  min-height:100%;
}
.canvas-container {
   width: 100%;
   text-align:center;
}
</style>
</head>
<body>
<div class="canvas-container">
  <button id="prev">&lt;</button>
  <span>Page: <span id="page_num"></span> / <span id="page_count"></span></span>
  <button id="next">&gt;</button>
  @if (!in_array('pdf',explode(',',env('DOWNLOAD_DISABLED_EXTENSIONS'))))
  <a href="/collection/{{ $collection_id }}/document/{{ $document_id }}">Download</a>
  @endif
</div>
<div class="canvas-container">
  <canvas id="the-canvas"></canvas>
</div>

<script>
// If absolute URL from the remote server is provided, configure the CORS
// header on that server.
var url = '/collection/{{ $collection_id }}/document/{{ $document_id }}';

// Loaded via <script> tag, create shortcut to access PDF.js exports.
var pdfjsLib = window['pdfjs-dist/build/pdf'];

// The workerSrc property shall be specified.
pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/pdf.worker.mjs';

var pdfDoc = null,
    pageNum = 1,
    pageRendering = false,
    pageNumPending = null,
    scale = 2,
    canvas = document.getElementById('the-canvas'),
    ctx = canvas.getContext('2d');

/**
 * Get page info from document, resize canvas accordingly, and render page.
 * @param num Page number.
 */
function renderPage(num) {
  pageRendering = true;
  // Using promise to fetch the page
  pdfDoc.getPage(num).then(function(page) {
    var viewport = page.getViewport({scale: scale});
    canvas.height = viewport.height;
    canvas.width = viewport.width;

    // Render PDF page into canvas context
    var renderContext = {
      canvasContext: ctx,
      viewport: viewport
    };
    var renderTask = page.render(renderContext);

    // Wait for rendering to finish
    renderTask.promise.then(function() {
      pageRendering = false;
      if (pageNumPending !== null) {
        // New page rendering is pending
        renderPage(pageNumPending);
        pageNumPending = null;
      }
    });
  });

  // Update page counters
  document.getElementById('page_num').textContent = num;
}

/**
 * If another page rendering in progress, waits until the rendering is
 * finised. Otherwise, executes rendering immediately.
 */
function queueRenderPage(num) {
  if (pageRendering) {
    pageNumPending = num;
  } else {
    renderPage(num);
  }
}

/**
 * Displays previous page.
 */
function onPrevPage() {
  if (pageNum <= 1) {
    return;
  }
  pageNum--;
  queueRenderPage(pageNum);
}
document.getElementById('prev').addEventListener('click', onPrevPage);

/**
 * Displays next page.
 */
function onNextPage() {
  if (pageNum >= pdfDoc.numPages) {
    return;
  }
  pageNum++;
  queueRenderPage(pageNum);
}
document.getElementById('next').addEventListener('click', onNextPage);

/**
 * Asynchronously downloads PDF.
 */
pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
  pdfDoc = pdfDoc_;
  document.getElementById('page_count').textContent = pdfDoc.numPages;

  // Initial/first page rendering
  renderPage(pageNum);
});
</script>
</body>
</html>
