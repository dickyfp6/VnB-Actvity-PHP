#!/usr/bin/env python3
import re
import os
from pathlib import Path
from collections import defaultdict

# Define project root
PROJECT_ROOT = Path(__file__).parent

# ==================== EXTRACT ROUTES ====================
def extract_routes_from_files():
    """Extract all defined routes from web.php and api.php"""
    routes = {
        'web': [],
        'api': []
    }

    # Read web.php
    web_file = PROJECT_ROOT / 'routes' / 'web.php'
    if web_file.exists():
        with open(web_file, 'r', encoding='utf-8') as f:
            content = f.read()
            # Extract route patterns
            patterns = [
                r"Route::(get|post|put|delete|patch)\('([^']+)'",
                r'Route::(get|post|put|delete|patch)\("([^"]+)"',
                r"->name\('([^']+)'\)",
            ]

            # Get route names
            for match in re.finditer(r"->name\('([^']+)'\)", content):
                routes['web'].append({
                    'name': match.group(1),
                    'type': 'named'
                })

            # Get routes without explicit names (by path)
            for match in re.finditer(r"Route::(get|post|put|delete|patch)\('([^']+)'", content):
                method = match.group(1)
                path = match.group(2)
                routes['web'].append({
                    'path': path,
                    'method': method,
                    'type': 'unnamed'
                })

    # Read api.php
    api_file = PROJECT_ROOT / 'routes' / 'api.php'
    if api_file.exists():
        with open(api_file, 'r', encoding='utf-8') as f:
            content = f.read()

            # Get route names
            for match in re.finditer(r"->name\('([^']+)'\)", content):
                routes['api'].append({
                    'name': match.group(1),
                    'type': 'named'
                })

            # Get routes by path
            for match in re.finditer(r"Route::(get|post|put|delete|patch)\('([^']+)'", content):
                method = match.group(1)
                path = match.group(2)
                routes['api'].append({
                    'path': path,
                    'method': method,
                    'type': 'unnamed'
                })

    return routes

# ==================== EXTRACT ROUTE CALLS ====================
def extract_route_calls():
    """Extract all route() calls and API calls from views and controllers"""
    called_routes = set()

    # Search in views for route() calls
    views_dir = PROJECT_ROOT / 'resources' / 'views'
    if views_dir.exists():
        for blade_file in views_dir.rglob('*.blade.php'):
            with open(blade_file, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
                # Find route('name') calls
                for match in re.finditer(r"route\('([^']+)'\)", content):
                    called_routes.add(('route', match.group(1)))
                # Find direct URL references to /api/
                for match in re.finditer(r"['\"](/api/[^'\"]+)['\"]", content):
                    called_routes.add(('api_url', match.group(1)))

    # Search in controllers
    controllers_dir = PROJECT_ROOT / 'app' / 'Http' / 'Controllers'
    if controllers_dir.exists():
        for php_file in controllers_dir.rglob('*.php'):
            with open(php_file, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
                # Find route('name') calls
                for match in re.finditer(r"route\('([^']+)'\)", content):
                    called_routes.add(('route', match.group(1)))

    return called_routes

# ==================== MAIN ANALYSIS ====================
def main():
    print("=" * 80)
    print("ANALYZING UNUSED ROUTES IN PROJECT")
    print("=" * 80)

    routes = extract_routes_from_files()
    called_routes = extract_route_calls()

    print(f"\n📋 TOTAL ROUTES EXTRACTED:")
    print(f"  - Web routes: {len(routes['web'])}")
    print(f"  - API routes: {len(routes['api'])}")
    print(f"\n📞 TOTAL ROUTE CALLS FOUND: {len(called_routes)}")

    # Extract unique route names that are called
    called_named_routes = {name for route_type, name in called_routes if route_type == 'route'}
    called_api_urls = {url for route_type, url in called_routes if route_type == 'api_url'}

    print(f"\n✅ CALLED NAMED ROUTES: {len(called_named_routes)}")
    for route in sorted(called_named_routes):
        print(f"   - {route}")

    print(f"\n✅ CALLED API ENDPOINTS: {len(called_api_urls)}")
    for url in sorted(called_api_urls):
        print(f"   - {url}")

    # Find potentially unused named routes
    defined_named_routes = {r.get('name') for r in routes['web'] if 'name' in r}
    defined_named_routes.update({r.get('name') for r in routes['api'] if 'name' in r})

    print(f"\n❓ POTENTIALLY UNUSED NAMED ROUTES:")
    unused_named = defined_named_routes - called_named_routes
    if unused_named:
        for route in sorted(unused_named):
            if route:  # Skip empty ones
                print(f"   - {route}")
    else:
        print("   ✓ All named routes appear to be used!")

    # Check for API routes not being called
    print(f"\n❓ POTENTIALLY UNUSED API PATHS:")
    defined_api_paths = {r.get('path') for r in routes['api'] if 'path' in r}

    # Simplify paths for comparison (remove parameters)
    def simplify_path(path):
        return re.sub(r'\{[^}]+\}', ':param', path)

    unused_api = []
    for api_path in sorted(defined_api_paths):
        if api_path:
            # Check if this path (or variations) is being called
            simplified = simplify_path(api_path)
            found = False
            for called_url in called_api_urls:
                if api_path in called_url or simplified in called_url:
                    found = True
                    break
            if not found:
                unused_api.append(api_path)

    if unused_api:
        for path in sorted(unused_api)[:20]:  # Show first 20
            print(f"   - {path}")
        if len(unused_api) > 20:
            print(f"   ... and {len(unused_api) - 20} more")
    else:
        print("   ✓ All API paths appear to be used!")

    print("\n" + "=" * 80)
    print("SUMMARY")
    print("=" * 80)
    print(f"Routes that may not be called: {len(unused_named) + len(unused_api)}")

if __name__ == '__main__':
    main()
