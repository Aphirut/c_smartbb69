import sys
from pathlib import Path

pdf_path = Path(r"C:\laragon\www\c_smartbb69\แบบฟอร์ม\แบบ พต.1.pdf")

try:
    # Try pypdf first (modern)
    try:
        from pypdf import PdfReader
        reader = PdfReader(str(pdf_path))
        print(f"Total pages: {len(reader.pages)}")
        print("="*60)
        for i, page in enumerate(reader.pages[:3]):  # First 3 pages
            text = page.extract_text()
            print(f"\n--- Page {i+1} ---")
            print(text[:2000] if text else "(No text)")
        print("\n... (truncated)")
    except ImportError:
        # Fallback to PyPDF2
        try:
            import PyPDF2
            with open(pdf_path, 'rb') as f:
                reader = PyPDF2.PdfReader(f)
                print(f"Total pages: {len(reader.pages)}")
                for i, page in enumerate(reader.pages[:3]):
                    text = page.extract_text()
                    print(f"\n--- Page {i+1} ---")
                    print(text[:2000] if text else "(No text)")
        except ImportError:
            print("No PDF library available (pypdf or PyPDF2)")
            sys.exit(1)
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()