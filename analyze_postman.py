import json
import sys

file_path = 'FitArt_JPAS_API_Complete.postman_collection.json'

try:
    with open(file_path, 'r', encoding='utf-8') as f:
        data = json.load(f)
    print("✅ JSON loaded successfully")
    print(f"Collection name: {data.get('info', {}).get('name', 'Unknown')}")
    print(f"Total modules: {len(data.get('item', []))}")
    print()
    
    # Extract endpoints
    def extract_endpoints(items, module='', parent='', endpoints=None):
        if endpoints is None:
            endpoints = []
        
        for item in items:
            item_name = item.get('name', 'Unknown')
            
            # Check if this is a folder
            if 'item' in item and isinstance(item['item'], list) and len(item['item']) > 0:
                # Recursively process children
                extract_endpoints(item['item'], item_name, item.get('name', ''), endpoints)
            elif 'request' in item:
                # This is an endpoint
                request = item['request']
                method = request.get('method', 'GET').upper()
                
                # Extract path
                path = ''
                if isinstance(request.get('url'), str):
                    path = request['url'].replace('{{base_url}}', '')
                elif isinstance(request.get('url'), dict):
                    if 'raw' in request['url']:
                        path = request['url']['raw'].replace('{{base_url}}', '')
                    elif 'path' in request['url']:
                        path = '/' + '/'.join(request['url']['path'])
                
                # Check for response
                has_response = False
                response_count = 0
                if 'response' in item and isinstance(item['response'], list):
                    response_count = len(item['response'])
                    for resp in item['response']:
                        if resp.get('body'):
                            has_response = True
                            break
                
                if path:
                    endpoints.append({
                        'method': method,
                        'path': path,
                        'name': item_name,
                        'module': module,
                        'has_response': has_response,
                        'response_count': response_count,
                        'status': 'TESTED' if has_response else 'UNTESTED'
                    })
        
        return endpoints
    
    endpoints = extract_endpoints(data.get('item', []))
    
    print(f"Total endpoints extracted: {len(endpoints)}")
    tested = sum(1 for e in endpoints if e['status'] == 'TESTED')
    untested = sum(1 for e in endpoints if e['status'] == 'UNTESTED')
    print(f"Tested: {tested}")
    print(f"Untested: {untested}")
    print(f"Coverage: {(tested / len(endpoints) * 100):.1f}%" if endpoints else "N/A")
    print()
    
    # Group by module
    by_module = {}
    for ep in endpoints:
        mod = ep['module']
        if mod not in by_module:
            by_module[mod] = {'tested': 0, 'untested': 0}
        if ep['status'] == 'TESTED':
            by_module[mod]['tested'] += 1
        else:
            by_module[mod]['untested'] += 1
    
    print("Coverage by module:")
    for mod in sorted(by_module.keys()):
        counts = by_module[mod]
        total = counts['tested'] + counts['untested']
        pct = (counts['tested'] / total * 100) if total > 0 else 0
        print(f"  {mod}: {counts['tested']}/{total} ({pct:.1f}%)")
    
except json.JSONDecodeError as e:
    print(f"❌ JSON Decode Error: {e}")
    print(f"Line: {e.lineno}, Column: {e.colno}")
except Exception as e:
    print(f"❌ Error: {e}")
    sys.exit(1)
