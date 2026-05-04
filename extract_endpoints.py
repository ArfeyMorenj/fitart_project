import json
import sys

def extract_endpoints(items, parent_group=''):
    """Recursively extract all endpoints from Postman collection"""
    endpoints = []
    
    for item in items:
        # If item has a request, it's an endpoint
        if 'request' in item:
            method = item['request'].get('method', 'N/A')
            url = item['request']['url'].get('raw', '').replace('{{base_url}}', '')
            
            # Extract query parameters
            params = []
            if 'query' in item['request']['url']:
                for query_param in item['request']['url']['query']:
                    if 'key' in query_param:
                        params.append(query_param['key'])
            
            # Extract path variables
            if 'variable' in item['request']['url']:
                for var in item['request']['url']['variable']:
                    if 'key' in var:
                        params.append(f":{var['key']}")
            
            endpoints.append({
                'group': parent_group,
                'name': item.get('name', 'N/A'),
                'method': method,
                'url': url,
                'parameters': params
            })
        
        # If item has nested items, recurse
        if 'item' in item:
            new_group = f"{parent_group} > {item.get('name', '')}" if parent_group else item.get('name', '')
            endpoints.extend(extract_endpoints(item['item'], new_group))
    
    return endpoints

# Load the collection
with open('FitArt_JPAS_API_Complete.postman_collection.json', 'r', encoding='utf-8') as f:
    collection = json.load(f)

# Extract all endpoints
endpoints = extract_endpoints(collection.get('item', []))

# Organize by group
grouped = {}
for endpoint in endpoints:
    group = endpoint['group']
    if group not in grouped:
        grouped[group] = []
    grouped[group].append(endpoint)

# Print formatted output
for group in sorted(grouped.keys()):
    print(f"\n{'='*80}")
    print(f"GROUP: {group}")
    print(f"{'='*80}")
    
    for i, endpoint in enumerate(grouped[group], 1):
        print(f"\n{i}. {endpoint['name']}")
        print(f"   Method: {endpoint['method']}")
        print(f"   URL: {endpoint['url']}")
        if endpoint['parameters']:
            print(f"   Parameters: {', '.join(endpoint['parameters'])}")
        else:
            print(f"   Parameters: None")

# Also print summary statistics
print(f"\n\n{'='*80}")
print("SUMMARY")
print(f"{'='*80}")
print(f"Total Groups: {len(grouped)}")
print(f"Total Endpoints: {len(endpoints)}")
print(f"\nEndpoints by Group:")
for group in sorted(grouped.keys()):
    print(f"  {group}: {len(grouped[group])} endpoints")
