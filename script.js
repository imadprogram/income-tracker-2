const income_btn = document.getElementById('add_income')
const expense_btn = document.getElementById('expense-btn')

const income_form_bg = document.querySelector('.transaction-form-bg')
const expense_form_bg = document.querySelector('.expense-form-bg')

const income_form = income_form_bg.querySelector('form')
// const expense_form = expense_form_bg.querySelector('form')

income_btn.addEventListener('click', () => {
    income_form_bg.classList.remove('hidden')
    income_form.classList.add('[animation:form-animation_.5s_ease]')
})
// expense_btn.addEventListener('click', () => {
//     expense_form_bg.classList.remove('hidden')
//     expense_form.classList.add('[animation:form-animation_.5s_ease]')
// })


// income_form_bg.addEventListener('click', () => {
//     income_form.classList.remove('[animation:form-animation_.5s_ease]')
//     income_form_bg.classList.add('hidden')
// })
// expense_form_bg.addEventListener('click', () => {
//     expense_form.classList.remove('[animation:form-animation_.5s_ease]')
//     expense_form_bg.classList.add('hidden')
// })


// income_form.addEventListener('click', (e) => { e.stopPropagation() })
// expense_form.addEventListener('click', (e) => { e.stopPropagation() })

// const edit = document.querySelectorAll('.edit')

// edit.forEach(ed => {
//     ed.addEventListener('click', (e) => {
//         if (ed.closest('#incomes')) {
//             let row = e.target.closest('.element')
//             let edit_form = document.createElement('div')
//             edit_form.className = "bg-[rgba(0,0,0,.1)] fixed backdrop-blur-sm w-full h-[100vh] flex justify-center items-center top-0"

//             edit_form.innerHTML = `
//                 <form action="./dashboard.php" method="post" class="top-1/2 left-1/2  bg-white shadow-xl rounded-lg px-6 py-4 flex flex-col gap-1">
//                     <input name="id" type="hidden" value="${row.dataset.id}" >
//                     <label>amount:</label>
//                     <input required type="text" name="income-new-amount" placeholder="100.00" class="bg-gray-200 rounded-lg w-80 py-2 pl-3"><br><br>
//                     <label>description:</label>
//                     <input required type="text" name="income-new-description" placeholder="from ?" class="bg-gray-200 rounded-lg w-80 py-2 pl-3"><br><br>
//                     <label>date:</label>
//                     <input type="date" name="income-new-date" class="bg-gray-200 rounded-lg w-80 py-2 pl-3"><br><br>
//                     <div class='flex gap-2'>
//                         <button class='grow border rounded-lg border-gray-400'>Cancel</button>
//                         <input type="submit" name="income-new-submit" class="grow bg-green-400 rounded-lg py-2 text-white">
//                     </div>
//                 </form>`
//             edit_form.querySelector('button').addEventListener('click', () => {
//                 edit_form.classList.add('hidden')
//             })
//             document.body.appendChild(edit_form)
//             edit_form.addEventListener('click', () => {
//                 edit_form.classList.add('hidden')
//             })
//             edit_form.querySelector('form').addEventListener('click', (e) => {
//                 e.stopPropagation()
//             })
//         } else if (ed.closest('#expenses')) {
//             let row = e.target.closest('.element')
//             let edit_form = document.createElement('div')
//             edit_form.className = "bg-[rgba(0,0,0,.1)] fixed backdrop-blur-sm w-full h-[100vh] flex justify-center items-center top-0"

//             edit_form.innerHTML = `
//                 <form action="./dashboard.php" method="post" class="top-1/2 left-1/2  bg-white shadow-xl rounded-lg px-6 py-4 flex flex-col gap-1">
//                     <input name="id" type="hidden" value="${row.dataset.id}" >
//                     <label>amount:</label>
//                     <input required type="text" name="expense-new-amount" placeholder="100.00" class="bg-gray-200 rounded-lg w-80 py-2 pl-3"><br><br>
//                     <label>description:</label>
//                     <input required type="text" name="expense-new-description" placeholder="from ?" class="bg-gray-200 rounded-lg w-80 py-2 pl-3"><br><br>
//                     <label>date:</label>
//                     <input type="date" name="expense-new-date" class="bg-gray-200 rounded-lg w-80 py-2 pl-3"><br><br>
//                     <div class='flex gap-2'>
//                         <button class='grow border rounded-lg border-gray-400'>Cancel</button>
//                         <input type="submit" name="expense-new-submit" class="grow bg-green-400 rounded-lg py-2 text-white">
//                     </div>
//                 </form>`
//             edit_form.querySelector('button').addEventListener('click', () => {
//                 edit_form.classList.add('hidden')
//             })
//             document.body.appendChild(edit_form)
//             edit_form.addEventListener('click', () => {
//                 edit_form.classList.add('hidden')
//             })
//             edit_form.querySelector('form').addEventListener('click', (e) => {
//                 e.stopPropagation()
//             })
//         }
//     })
// })

// const bin = document.querySelectorAll('.bin')

// bin.forEach(b => {
//     b.addEventListener('click', (e) => {
//         if (b.closest('#incomes')) {
//             let row = e.target.closest('.element')
//             let del_form = document.createElement('div')
//             del_form.className = "bg-[rgba(0,0,0,.1)] fixed backdrop-blur-sm w-full h-[100vh] flex justify-center items-center top-0"

//             del_form.innerHTML = `
//                 <form action="./dashboard.php" method="post" class="top-1/2 left-1/2  bg-white shadow-xl rounded-lg px-6 py-4 flex flex-col gap-3 text-center">
//                     <input name="id" type="hidden" value="${row.dataset.id}" >
//                     <h1 class='text-lg font-bold'>Delete</h1>
//                     <p class=' text-gray-500'>Are You Sure You Want To Delete ?</p>
//                     <div class='flex gap-4'>
//                         <input type="submit" name="income-delete" value='confirm' class="grow bg-red-600 rounded-lg py-2 text-white cursor-pointer">
//                         <button class='grow border rounded-lg border-gray-400'>Cancel</button>
//                     </div>
//                 </form>`
//             del_form.querySelector('button').addEventListener('click', () => {
//                 del_form.classList.add('hidden')
//             })
//             document.body.appendChild(del_form)
//             console.log(row.dataset.id)
//         } else if (b.closest('#expenses')) {
//             let row = e.target.closest('.element')
//             let del_form = document.createElement('div')
//             del_form.className = "bg-[rgba(0,0,0,.1)] fixed backdrop-blur-sm w-full h-[100vh] flex justify-center items-center top-0"

//             del_form.innerHTML = `
//                 <form action="./dashboard.php" method="post" class="top-1/2 left-1/2  bg-white shadow-xl rounded-lg px-6 py-4 flex flex-col gap-3 text-center">
//                     <input name="id" type="hidden" value="${row.dataset.id}" >
//                     <h1 class='text-lg font-bold'>Delete</h1>
//                     <p class=' text-gray-500'>Are You Sure You Want To Delete ?</p>
//                     <div class='flex gap-4'>
//                         <input type="submit" name="expense-delete" value='confirm' class="grow bg-red-600 rounded-lg py-2 text-white cursor-pointer">
//                         <button class='grow border rounded-lg border-gray-400'>Cancel</button>
//                     </div>
//                 </form>`
//             del_form.querySelector('button').addEventListener('click', () => {
//                 del_form.classList.add('hidden')
//             })
//             document.body.appendChild(del_form)
//         }
//     })
// })




// // ... (Your existing UI logic: income_btn listeners, edit/bin handlers) ...

// // chart js
// const ctx = document.getElementById("myChart");

// // **Accessing Data from PHP**
// const incomeData = incomeDataFromPHP;
// const expenseData = expenseDataFromPHP;

// // Helper function to process data and ensure sorting by date
// const prepareChartData = (data) => {
//     // Sort data by date
//     data.sort((a, b) => new Date(a.date) - new Date(b.date));
//     return data; 
// };

// // Process data to ensure they are sorted
// const processedIncomeData = prepareChartData(incomeData);
// const processedExpenseData = prepareChartData(expenseData);


// // Combine all unique dates for the X-axis labels (ensures both datasets align)
// const allDates = [
//     ...new Set([
//         ...processedIncomeData.map(d => d.date), 
//         ...processedExpenseData.map(d => d.date)
//     ])
// ];
// allDates.sort((a, b) => new Date(a) - new Date(b));

// // Format dates nicely for the labels
// const chartLabels = allDates.map(date => new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));


// // Function to map amounts to the combined dates, using 0 if no transaction occurred on that day
// const mapAmountsToLabels = (rawData, allDates) => {
//     const amountMap = new Map(rawData.map(item => [item.date, item.amount]));
//     return allDates.map(date => amountMap.get(date) || 0); 
// };

// const incomeChartData = mapAmountsToLabels(processedIncomeData, allDates);
// const expenseChartData = mapAmountsToLabels(processedExpenseData, allDates);


// new Chart(ctx, {
//     type: 'line',
//     data: {
//         labels: chartLabels, // Use the combined, sorted unique dates
//         datasets: [
//             {
//                 label: 'Income',
//                 data: incomeChartData,
//                 borderColor: '#10B981', // Green-500
//                 backgroundColor: 'rgba(16, 185, 129, 0.2)',
//                 tension: 0.4,
//                 fill: true,
//                 pointRadius: 4,
//             },
//             {
//                 label: 'Expense',
//                 data: expenseChartData,
//                 borderColor: '#EF4444', // Red-500
//                 backgroundColor: 'rgba(239, 68, 68, 0.2)',
//                 tension: 0.4,
//                 fill: true,
//                 pointRadius: 4,
//             }
//         ]
//     },
//     options: {
//         responsive: true,
//         maintainAspectRatio: false,
//         scales: {
//             y: {
//                 beginAtZero: true,
//                 title: {
//                     display: true,
//                     text: 'Amount ($)'
//                 }
//             },
//             x: {
//                 title: {
//                     display: true,
//                     text: 'Date'
//                 },
//                 type: 'category',
//                 labels: chartLabels
//             }
//         },
//         plugins: {
//             legend: {
//                 position: 'top',
//             },
//             title: {
//                 display: true,
//                 text: 'Monthly Income vs. Expense Trend'
//             }
//         }
//     }
// });


