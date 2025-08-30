#!/bin/bash

echo "Converting Bootstrap classes to Tailwind CSS in admin views..."

# Function to convert Bootstrap classes to Tailwind CSS
convert_bootstrap() {
    local file="$1"
    echo "Converting $file..."
    
    # Convert Bootstrap button classes to Tailwind CSS
    sed -i 's/class="btn btn-primary"/class="inline-flex items-center px-4 py-2 bg-action text-primary font-medium rounded-lg hover:bg-action\/90 transition-colors"/g' "$file"
    sed -i 's/class="btn btn-success"/class="inline-flex items-center px-4 py-2 bg-green-500 text-white font-medium rounded-lg hover:bg-green-600 transition-colors"/g' "$file"
    sed -i 's/class="btn btn-warning"/class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white font-medium rounded-lg hover:bg-yellow-600 transition-colors"/g' "$file"
    sed -i 's/class="btn btn-danger"/class="inline-flex items-center px-4 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition-colors"/g' "$file"
    sed -i 's/class="btn btn-info"/class="inline-flex items-center px-4 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition-colors"/g' "$file"
    sed -i 's/class="btn btn-secondary"/class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors"/g' "$file"
    
    # Convert Bootstrap outline button classes
    sed -i 's/class="btn btn-outline-primary"/class="inline-flex items-center px-4 py-2 bg-transparent border border-action text-action font-medium rounded-lg hover:bg-action hover:text-primary transition-colors"/g' "$file"
    sed -i 's/class="btn btn-outline-success"/class="inline-flex items-center px-4 py-2 bg-transparent border border-green-500 text-green-600 font-medium rounded-lg hover:bg-green-500 hover:text-white transition-colors"/g' "$file"
    sed -i 's/class="btn btn-outline-warning"/class="inline-flex items-center px-4 py-2 bg-transparent border border-yellow-500 text-yellow-600 font-medium rounded-lg hover:bg-yellow-500 hover:text-white transition-colors"/g' "$file"
    sed -i 's/class="btn btn-outline-danger"/class="inline-flex items-center px-4 py-2 bg-transparent border border-red-500 text-red-600 font-medium rounded-lg hover:bg-red-500 hover:text-white transition-colors"/g' "$file"
    sed -i 's/class="btn btn-outline-info"/class="inline-flex items-center px-4 py-2 bg-transparent border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-500 hover:text-white transition-colors"/g' "$file"
    sed -i 's/class="btn btn-outline-secondary"/class="inline-flex items-center px-4 py-2 bg-transparent border border-gray-500 text-gray-600 font-medium rounded-lg hover:bg-gray-500 hover:text-white transition-colors"/g' "$file"
    
    # Convert Bootstrap small button classes
    sed -i 's/class="btn btn-sm btn-primary"/class="inline-flex items-center px-3 py-1.5 bg-action text-primary text-sm font-medium rounded hover:bg-action\/90 transition-colors"/g' "$file"
    sed -i 's/class="btn btn-sm btn-success"/class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-sm font-medium rounded hover:bg-green-600 transition-colors"/g' "$file"
    sed -i 's/class="btn btn-sm btn-warning"/class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white text-sm font-medium rounded hover:bg-yellow-600 transition-colors"/g' "$file"
    sed -i 's/class="btn btn-sm btn-danger"/class="inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded hover:bg-red-600 transition-colors"/g' "$file"
    sed -i 's/class="btn btn-sm btn-info"/class="inline-flex items-center px-3 py-1.5 bg-blue-500 text-white text-sm font-medium rounded hover:bg-blue-600 transition-colors"/g' "$file"
    sed -i 's/class="btn btn-sm btn-secondary"/class="inline-flex items-center px-3 py-1.5 bg-gray-500 text-white text-sm font-medium rounded hover:bg-gray-600 transition-colors"/g' "$file"
    
    # Convert Bootstrap small outline button classes
    sed -i 's/class="btn btn-sm btn-outline-primary"/class="inline-flex items-center px-3 py-1.5 bg-transparent border border-action text-action text-sm font-medium rounded hover:bg-action hover:text-primary transition-colors"/g' "$file"
    sed -i 's/class="btn btn-sm btn-outline-success"/class="inline-flex items-center px-3 py-1.5 bg-transparent border border-green-500 text-green-600 text-sm font-medium rounded hover:bg-green-500 hover:text-white transition-colors"/g' "$file"
    sed -i 's/class="btn btn-sm btn-outline-warning"/class="inline-flex items-center px-3 py-1.5 bg-transparent border border-yellow-500 text-yellow-600 text-sm font-medium rounded hover:bg-yellow-500 hover:text-white transition-colors"/g' "$file"
    sed -i 's/class="btn btn-sm btn-outline-danger"/class="inline-flex items-center px-3 py-1.5 bg-transparent border border-red-500 text-red-600 text-sm font-medium rounded hover:bg-red-500 hover:text-white transition-colors"/g' "$file"
    sed -i 's/class="btn btn-sm btn-outline-info"/class="inline-flex items-center px-3 py-1.5 bg-transparent border border-blue-500 text-blue-600 text-sm font-medium rounded hover:bg-blue-500 hover:text-white transition-colors"/g' "$file"
    sed -i 's/class="btn btn-sm btn-outline-secondary"/class="inline-flex items-center px-3 py-1.5 bg-transparent border border-gray-500 text-gray-600 text-sm font-medium rounded hover:bg-gray-500 hover:text-white transition-colors"/g' "$file"
    
    # Convert Bootstrap alert classes
    sed -i 's/class="alert alert-success"/class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative"/g' "$file"
    sed -i 's/class="alert alert-danger"/class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative"/g' "$file"
    sed -i 's/class="alert alert-warning"/class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg relative"/g' "$file"
    sed -i 's/class="alert alert-info"/class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg relative"/g' "$file"
    
    # Convert Bootstrap card classes
    sed -i 's/class="card"/class="bg-white rounded-lg shadow-md"/g' "$file"
    sed -i 's/class="card-body"/class="p-6"/g' "$file"
    
    # Convert Bootstrap table classes
    sed -i 's/class="table"/class="min-w-full divide-y divide-gray-200"/g' "$file"
    sed -i 's/class="table-hover"/class="divide-y divide-gray-200"/g' "$file"
    sed -i 's/class="table-striped"/class="divide-y divide-gray-200"/g' "$file"
    sed -i 's/class="table-responsive"/class="overflow-x-auto"/g' "$file"
    
    # Convert Bootstrap badge classes
    sed -i 's/class="badge bg-info"/class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"/g' "$file"
    sed -i 's/class="badge bg-warning"/class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"/g' "$file"
    sed -i 's/class="badge bg-success"/class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"/g' "$file"
    sed -i 's/class="badge bg-danger"/class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"/g' "$file"
    sed -i 's/class="badge bg-primary"/class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary text-white"/g' "$file"
    sed -i 's/class="badge bg-secondary"/class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"/g' "$file"
    
    # Convert Bootstrap form classes
    sed -i 's/class="form-control"/class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm"/g' "$file"
    sed -i 's/class="form-select"/class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-action focus:ring-action sm:text-sm"/g' "$file"
    
    # Convert Bootstrap grid classes
    sed -i 's/class="row"/class="grid grid-cols-1 lg:grid-cols-12 gap-6"/g' "$file"
    sed -i 's/class="col-md-2"/class="lg:col-span-2"/g' "$file"
    sed -i 's/class="col-md-4"/class="lg:col-span-4"/g' "$file"
    sed -i 's/class="col-md-6"/class="lg:col-span-6"/g' "$file"
    sed -i 's/class="col-md-8"/class="lg:col-span-8"/g' "$file"
    sed -i 's/class="col-md-12"/class="lg:col-span-12"/g' "$file"
    
    # Convert Bootstrap utility classes
    sed -i 's/class="d-flex"/class="flex"/g' "$file"
    sed -i 's/class="d-inline"/class="inline"/g' "$file"
    sed -i 's/class="d-block"/class="block"/g' "$file"
    sed -i 's/class="d-none"/class="hidden"/g' "$file"
    sed -i 's/class="justify-content-center"/class="justify-center"/g' "$file"
    sed -i 's/class="justify-content-between"/class="justify-between"/g' "$file"
    sed -i 's/class="align-items-center"/class="items-center"/g' "$file"
    sed -i 's/class="text-center"/class="text-center"/g' "$file"
    sed -i 's/class="text-muted"/class="text-gray-500"/g' "$file"
    sed -i 's/class="mb-0"/class="mb-0"/g' "$file"
    sed -i 's/class="mb-4"/class="mb-4"/g' "$file"
    sed -i 's/class="me-2"/class="mr-2"/g' "$file"
    sed -i 's/class="me-3"/class="mr-3"/g' "$file"
    sed -i 's/class="ms-auto"/class="ml-auto"/g' "$file"
    
    # Convert Bootstrap button group
    sed -i 's/class="btn-group"/class="flex space-x-2"/g' "$file"
    sed -i 's/role="group"/class="flex space-x-2"/g' "$file"
    
    # Convert Bootstrap close button
    sed -i 's/class="btn-close"/class="absolute top-0 right-0 mt-2 mr-2 text-gray-700 hover:text-gray-900"/g' "$file"
    sed -i 's/data-bs-dismiss="modal"/onclick="this.parentElement.remove()"/g' "$file"
    
    # Convert Bootstrap modal attributes
    sed -i 's/data-bs-toggle="modal"//g' "$file"
    sed -i 's/data-bs-target="#clearLogsModal"/onclick="showClearLogsModal()"/g' "$file"
    
    echo "✅ Converted $file"
}

# Find all admin view files and convert them
find resources/views/admin -name "*.blade.php" -type f | while read -r file; do
    convert_bootstrap "$file"
done

echo "🎉 Bootstrap to Tailwind CSS conversion completed!"
echo "All admin views now use Tailwind CSS with your custom color scheme."

