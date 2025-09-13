import json
import sys

with open('lighthouse-performance-report.report.json', 'r') as f:
    data = json.load(f)

audits = data.get('audits', {})

# Unused CSS
unused_css = audits.get('unused-css-rules', {})
if unused_css.get('details', {}).get('items'):
    print("Unused CSS Files:")
    for item in unused_css['details']['items'][:3]:
        print(f"  - {item.get('url', 'Unknown')}: {item.get('wastedBytes', 0)/1024:.1f}KB wasted")

# Unused JavaScript
unused_js = audits.get('unused-javascript', {})
if unused_js.get('details', {}).get('items'):
    print("\nUnused JavaScript Files:")
    for item in unused_js['details']['items'][:3]:
        print(f"  - {item.get('url', 'Unknown')}: {item.get('wastedBytes', 0)/1024:.1f}KB wasted")

# Render-blocking resources
render_blocking = audits.get('render-blocking-resources', {})
if render_blocking.get('details', {}).get('items'):
    print("\nRender-blocking Resources:")
    for item in render_blocking['details']['items'][:3]:
        print(f"  - {item.get('url', 'Unknown')}: {item.get('wastedMs', 0)}ms")

# LCP element
lcp_element = audits.get('largest-contentful-paint-element', {})
if lcp_element.get('details', {}).get('items'):
    print("\nLCP Element:")
    item = lcp_element['details']['items'][0]
    print(f"  - Type: {item.get('node', {}).get('nodeLabel', 'Unknown')}")
    print(f"  - Selector: {item.get('node', {}).get('snippet', 'Unknown')}")

# Image optimization
modern_formats = audits.get('modern-image-formats', {})
if modern_formats.get('details', {}).get('items'):
    print("\nImages that could use modern formats:")
    for item in modern_formats['details']['items'][:3]:
        print(f"  - {item.get('url', 'Unknown')}: Could save {item.get('wastedBytes', 0)/1024:.1f}KB")

# Main thread work
main_thread = audits.get('mainthread-work-breakdown', {})
if main_thread.get('details', {}).get('items'):
    print("\nMain Thread Work Breakdown:")
    for item in main_thread['details']['items'][:5]:
        print(f"  - {item.get('groupLabel', 'Unknown')}: {item.get('duration', 0):.0f}ms")

print(f"\nOverall Performance Score: {data['categories']['performance']['score']*100:.0f}/100")
print(f"NFR Target Compliance:")
print(f"  - LCP: {audits['largest-contentful-paint']['numericValue']:.0f}ms (Target: ≤2500ms) {'✅' if audits['largest-contentful-paint']['numericValue'] <= 2500 else '❌'}")
print(f"  - CLS: {audits['cumulative-layout-shift']['numericValue']:.3f} (Target: ≤0.1) {'✅' if audits['cumulative-layout-shift']['numericValue'] <= 0.1 else '❌'}")
print(f"  - TTI: {audits.get('interactive', {}).get('numericValue', 0):.0f}ms (Target: ≤1500ms) {'✅' if audits.get('interactive', {}).get('numericValue', 0) <= 1500 else '❌'}")