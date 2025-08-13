### Dados

# Inputs
N = int(input('Digite o número de operações: '))

### Funções
def máximo(lista):
    a = 0
    for n in lista:
        for m in lista:
            if n >= m:
                a = n
            if n < m:
                a =m
    return a
def mínimo(lista):
    a = 0
    for n in lista:
        for m in lista:
            if n >= m:
                a = m
            if n < m:
                a = m
    return a
def pr_nota_horário(x):
    a = 0
    u = x['Horário']
    for i in range(N):
        v = D[f'Operação {i+1}']['Horário']
        if u >= v:
            a = a
        if u < v:
            a = a + 1
    return a*10/(N-1)
def pr_nota_fidelidade(x):
    a = 0
    u = x['Fidelidade']
    for i in range(N):
        v = D[f'Operação {i+1}']['Fidelidade']
        if u <= v:
            a = a
        if u > v:
            a = a + 1
    return a*10/(N-1)
def pr_nota_certezas(x):
    a = (pr_nota_horário(x) + pr_nota_fidelidade(x))/2
    return a
def pr_nota_incertezas(x):
    u = x['Chance de chuva']
    d = x['Condicional']
    a = (u*d+(1-u))*pr_nota_certezas(x)
    return a
def cam_nota_carga(x):
    a = 0
    for k in Disponíveis:
        b = x['Carga máxima']
        c = k['Carga máxima']
        if b >= c:
            a = a
        if b < c:
            a = a + 1
    return a*10/(len(Disponíveis) - 1)
def cam_nota_altura(x):
    a = 0
    for k in Disponíveis:
        b = x['Altura máxima']
        c = k['Altura máxima']
        if b >= c:
            a = a
        if b < c:
            a = a + 1
    return a*10/(len(Disponíveis) - 1)
def cam_nota_raio(x):
    a = 0
    for k in Disponíveis:
        b = x['Raio máximo']
        c = k['Raio máximo']
        if b >= c:
            a = a
        if b < c:
            a = a + 1
    return a*10/(len(Disponíveis) - 1)
def cam_nota_final(x):
    a = (cam_nota_carga(x) + cam_nota_altura(x) + cam_nota_raio(x))/3
    return a
def op_nota_exp(x):
    a = 0
    u = x['Caminhões']
    v = u.index(escolha_caminhão)
    p = x['Experiência']
    q = p[v]
    for i in lista_operadores_filtrados:
        b = i['Caminhões']
        c = b.index(escolha_caminhão)
        d = i['Experiência']
        e = d[c]
        if q <= e:
            a = a
        if q > e:
            a = a + 1
    return a*10/(len(lista_operadores_filtrados) - 1)
def op_nota_avaliação(x):
    a = 0
    for i in lista_operadores_filtrados:
        if x['Avaliação'] <= i['Avaliação']:
            a = a
        if x['Avaliação'] > i['Avaliação']:
            a = a + 1
    return a*10/(len(lista_operadores_filtrados) - 1)
def op_nota_final(x):
    return (op_nota_avaliação(x) + op_nota_exp(x))/2


# Operações
D = {}
for i in range(N):
    D[f'Operação {i+1}'] = {}
    D[f'Operação {i+1}']['Nome'] = str(input(f'Digite o nome da operação {i+1}: '))
    D[f'Operação {i+1}']['Carga da operação'] = float(input(f'Digite a carga da operação {i+1} (kg): '))
    D[f'Operação {i+1}']['Altura da operação'] = float(input(f'Digite a altura da operação {i+1} (m): '))
    D[f'Operação {i+1}']['Raio da operação'] = float(input(f'Digite o raio da operação {i+1} (m): ' ))
    D[f'Operação {i+1}']['Condicional'] = int(input(f'A operação {i+1} pode ocorrer com chuva? 0 para não, 1 para sim: '))
    D[f'Operação {i+1}']['Chance de chuva'] = float(input(f'Digite a chance de chuva no local onde a operação {i+1} será realizada (0-1.0): '))
    D[f'Operação {i+1}']['Horário'] = int(input(f'Digite o horário em que a operação {i+1} será realizada: '))
    D[f'Operação {i+1}']['Fidelidade'] = int(input(f'Digite o número de serviços realizados para esse cliente, contando com esse: '))
    D[f'Operação {i+1}']['Equipamento'] = str(input(f'Precisa de algum equipamento em especial para a operação {i+1}? Se sim, digite o nome, se não, digite 0: '))
    D[f'Operação {i+1}']['Operador'] = str(input(f'Precisa de algum operador em especial para a operação {i+1}? Se sim, digite o nome, se não, digite 0: '))
    aux = int(input(f'Precisa de auxiliar para a operação {i+1}? 0 para não, 1 para sim: '))
    if aux ==  1:
        D[f'Operação {i+1}']['Auxiliar'] = input(f'Precisa de algum auxiliar em especial para a operação {i+1}? Se sim, digite o nome, se não, digite 0: ')
    print()

# Caminhões
TKA_420EX = {'Nome' : 'TKA 420EX', 'Matriz de carga' : [[40000, 10, 2.7], [35000, 10, 3], [30000, 9, 3.5], [27000, 9, 4], [24300, 8, 4.5], [22000, 8, 5], [20000, 7, 5.5], [18200, 6.5, 6], [16800, 5.5, 6.5], [15600, 5, 7], [14000, 3, 7.5], [24200, 14, 3], [22800, 13.5, 3.5], [21500, 13.5, 4], [20600, 13, 4.5], [19100, 13, 5], [18000, 12.5, 5.5], [17000, 12, 6], [16000, 11.5, 6.5], [15000, 11, 7], [14500, 10.5, 7.5], [13300, 10, 8], [10500, 9, 9], [8200, 7, 10], [6600, 4, 11], [23800, 17.5, 3.5], [23800, 17.5, 4], [23800, 17, 4.5], [23800, 17, 5], [22600, 16.5, 5.5], [20700, 16.5, 6], [18500, 16, 6.5], [16500, 16, 7], [14800, 15.5, 7.5], [13200, 15, 8], [10100, 14, 9], [7900, 13.5, 10], [6300, 12, 11], [5000, 11, 12], [3200, 7, 14], [16000, 25, 4.5], [16000, 25, 5], [16000, 24.5, 5.5], [16000, 24.5, 6], [15900, 24, 6.5], [14600, 24, 7], [13100, 23.5, 7.5], [12100, 23.5, 8], [10700, 22.5, 9], [9500, 22.5, 10], [7800, 22, 11], [6400, 21.5, 12], [4500, 19.5, 14], [3200, 18, 16], [2200, 15, 18], [1500, 12, 20], [900, 5.5, 22], [11000, 32.5, 6], [11000, 32, 6.5], [11000, 32, 7], [11000, 31.5, 7.5], [10500, 31.5, 8], [9800, 31, 9], [8900, 31, 10], [8100, 30.5, 11], [7200, 30, 12], [5400, 29, 14], [4100, 27.5, 16], [3200, 26, 18], [2500, 24, 20], [1900, 22, 22], [1500, 19.5, 24], [1100, 16.5, 26], [800, 11.5, 28], [9000, 42, 7], [9000, 40, 7.5], [9000, 39.5, 8], [8300, 39, 9], [7600, 39, 10], [7000, 38.5, 11], [6300, 38, 12], [5300, 37, 14], [4300, 36, 16], [3300, 35, 18], [2500, 34, 20], [1900, 32, 22], [1400, 31, 24], [1000, 29, 26], [700, 26.5, 28], [400, 24, 30], [100, 21, 32]], 'Carga máxima' : 40000, 'Altura máxima' : 42, 'Raio máximo' : 32, 'Custo mínimo' : 'Definir'}
XCMG_25K = {'Nome' : 'XCMG 25K', 'Matriz de carga' : [[25000, 10.5, 3], [25000, 10.2, 3.5], [24200, 10.0, 4], [21800, 9.6, 4.5], [19100, 9.3, 5], [17300, 8.8, 5.5], [15800, 8.4, 6], [13800, 7.8, 6.5], [12200, 7.2, 7], [11000, 5.5, 8], [22000, 14.4, 3], [21500, 14.2, 3.5], [21000, 14.0, 4], [20600, 13.8, 4.5], [19200, 13.3, 5], [17800, 13.0, 5.5], [16100, 12.7, 6], [14100, 12.3, 6.5], [12800,	11.5, 7], [11000, 10.5, 8], [9080, 9.3, 9], [7430, 7.7, 10], [6220, 7, 11], [18000, 17.9, 4], [17100, 17.7, 4.5], [15800, 17.5, 5], [14800, 17.3, 5.5], [14000, 17.1, 6], [13000, 16.9, 6.5], [12200, 16.6, 7], [10800, 16.0, 8], [8900, 15.4, 9], [7300, 14.6, 10], [6100, 13.7, 11], [5100, 12.6, 12], [4200, 11.3, 13], [3550, 9.8, 14], [12000, 23.4, 4.5], [12000, 23.3, 5], [11700, 23.2, 5.5], [11000, 23.0, 6], [10800, 22.8, 6.5], [10200, 22.6, 7], [9200, 22.2, 8], [8400, 21.7, 9], [7560, 21.2, 10], [6700,	20.6, 11], [5700, 19.9, 12], [4900, 19.2, 13], [4200, 18.3, 14], [3620,	17.4, 15], [3100, 16.3, 16], [2300, 13.7, 18], [1700, 9.9, 20], [10400,	28.9, 5], [9700, 28.8, 5.5], [9400, 28.7, 6], [8600, 28.6, 6.5], [8350,	28.4, 7], [7900, 28.1, 8],  [7140, 27.7, 9], [6510, 27.3, 10], [5950, 26.8, 11], [5500, 26.3, 12], [5100, 25.8, 13], [4600, 25.2, 14], [4000, 24.5, 15], [3500, 23.8, 16], [2700, 22.1, 18], [2080, 20.0, 20], [1580, 17.5, 22], [1180, 14.2, 24], [7200, 34.2, 6], [7110, 34.1, 6.5], [6500, 33.8, 7], [6050, 33.5, 8], [5550, 33.2, 9], [5150, 32.8, 10], [4700, 32.4, 11], [4410, 31.9, 12], [4150, 31.5, 13], [3900, 30.9, 14], [3650, 30.4, 15], [2930, 29.1, 16], [2300, 27.6, 18], [1800, 25.8, 20], [1360, 23.8, 22], [980, 21.3, 24], [700, 18.3, 26], [5500, 39.2, 7], [5100, 38.9, 8], [4700, 38.6, 9], [4350, 38.3, 10], [4020, 37.9, 11], [3800, 37.5, 12], [3510, 37.0, 13], [3400, 36.6, 14], [2880, 35.5, 15], [2400, 34.3, 16], [1900, 32.9, 18], [1500, 31.3, 20], [1150, 29.5, 22], [800, 27.5, 24], [600, 25.05, 26], [500, 22.1, 28]], 'Carga máxima' : 25000, 'Altura máxima' : 39.5, 'Raio máximo' : 28, 'Custo mínimo' : 'Definir'}
IM_70 = {'Nome' : 'IM 70', 'Matriz de carga' : [[32300, 18.3, 2.1], [17500, 28.8, 3.9], [14870, 28.5, 4.7], [10380, 28, 6.4], [7810, 27.8, 8.2], [6810, 27, 10.1], [5070, 26, 12.1], [4020, 25.5, 14.4], [3380, 22.8, 16.5], [24500, 21.5, 18.5], [1500, 18.3, 20.7], [1000, 15, 22.9], [700, 4, 25.1]], 'Carga máxima' : 32300, 'Altura máxima' : 28.8, 'Raio máximo' : 25.1, 'Custo mínimo' : 'Definir'}
Argos_25000_ADVANCED = {'Nome' : 'Argos 25000 ADVANCED', 'Matriz de carga' : [[12500, 16, 2], [5700, 21.5, 4.3], [3845, 21, 6.3], [2810, 20, 8.4], [2220, 18.7, 10.4], [1820, 17, 12.4], [1300, 15, 14.4], [1000, 11.8, 16.4], [850, 3, 18.3]], 'Carga máxima' : 12500, 'Altura máxima' : 20, 'Raio máximo' : 18.3, 'Custo mínimo' : 'Definir'}
Argos_43000 = {'Nome' : 'Argos 43000', 'Matriz de carga' : [[9550, 21.3, 4.5], [7040, 20.6, 6.1], [5570, 19.8, 7.4], [3930, 19,  9.5], [3000, 17.5, 11.3], [1750, 15.5, 13.4], [1110, 12.4, 15.6], [850, 3, 17.9]], 'Carga máxima' : 9550, 'Altura máxima' : 21.6, 'Raio máximo' : 17.9, 'Custo mínimo' : 'Definir'}
Argos_40500 = {'Nome' : 'Argos 40500', 'Matriz de carga' : [[18410, 14, 2.2], [9000,  21.3, 4.5], [6570, 20.6, 6.1], [5380, 20, 7.4], [4175, 18.8, 9.5], [2830, 17.7, 11.3], [1750, 15.6, 13.4], [1110, 12.6, 15.6], [800, 3, 17.9]], 'Carga máxima' : 18410, 'Altura máxima' : 21.4, 'Raio máximo' : 17.9, 'Custo mínimo' : 'Definir'}
Argos_35000 = {'Nome' : 'Argos 35000', 'Matriz de carga' : [[14750, 14, 2.3], [7650, 18.3, 4.5], [5710, 17.6, 6.1], [4485, 16.6, 7.8], [3645, 15.5, 9.5], [2460, 13.7, 11.4], [1470, 11, 13.4], [980, 3, 15.3]], 'Carga máxima' : 14750, 'Altura máxima' : 18.6, 'Raio máximo' : 15.3, 'Custo mínimo' : 'Definir'}
lista_caminhões = [TKA_420EX, XCMG_25K, IM_70, Argos_25000_ADVANCED, Argos_43000, Argos_40500, Argos_35000, 'Empilhadeira']

# Operadores e Auxiliares
Valdeci = {'Nome' : 'Valdeci', 'Caminhões' : ['TKA 420EX', 'XCMG 25K', 'IM 70'], 'Experiência' : [10, 10, 8], 'Avaliação' : 10, 'Auxiliar' : False, 'Pode dirigir' : True}
Lucas = {'Nome' : 'Lucas', 'Caminhões' : ['XCMG 25K', 'IM 70'], 'Experiência' : [10, 10], 'Avaliação' : 10, 'Auxiliar' : False, 'Pode dirigir' : True}
Lindomar = {'Nome' : 'Lindomar', 'Caminhões' : ['Argos 25000 ADVANCED', 'Argos 43000', 'Argos 40500', 'Argos 35000'], 'Experiência' : [10, 6, 6, 8], 'Avaliação' : 10, 'Auxiliar' : False, 'Pode dirigir' : True}
Correa = {'Nome' : 'Correa', 'Caminhões' : ['XCMG 25K', 'IM 70', 'Argos 25000 ADVANCED', 'Argos 43000', 'Argos 40500', 'Argos 35000'], 'Experiência' : [8, 8, 8, 8, 8, 8], 'Avaliação' : 10, 'Auxiliar' : False, 'Pode dirigir' : True}
Everton = {'Nome' : 'Everton', 'Caminhões' : ['IM 70', 'Argos 25000 ADVANCED', 'Argos 43000', 'Argos 40500', 'Argos 35000'], 'Experiência' : [8, 4, 4, 4, 4], 'Avaliação' : 10, 'Auxiliar' : True, 'Pode dirigir' : True}
Nelson = {'Nome' : 'Nelson', 'Caminhões' : [], 'Experiência' : [], 'Avaliação' : 10, 'Auxiliar' : True, 'Pode dirigir' : True}
Valter = {'Nome' : 'Valter', 'Caminhões' : ['Empilhadeira'], 'Experiência' : [8], 'Avaliação' : 10, 'Auxiliar' : False, 'Pode dirigir' : False}
Jakson = {'Nome' : 'Jakson', 'Caminhões' : ['Argos 25000 ADVANCED', 'Argos 35000', 'Argos 43000'], 'Experiência' : [8, 8, 8], 'Avaliação' : 10, 'Auxiliar' : False, 'Pode dirigir' : True}
lista_operadores = [Valdeci, Lucas, Lindomar, Correa, Valter, Everton, Jakson, Nelson]
lista_auxiliares = [Nelson, Everton, Jakson]




## Prioridade
lista_notas_prioridades = []
E = {}
e = 0
for i in range(len(D)):
    l = D[f'Operação {i+1}']
    lista_notas_prioridades.append(pr_nota_incertezas(l))
while lista_notas_prioridades != []:
    for j in range(len(D)):
        if pr_nota_incertezas(D[f'Operação {j+1}']) == máximo(lista_notas_prioridades):
            E[f'{e}'] = D[f'Operação {j+1}']
            e = e + 1
            lista_notas_prioridades.remove(pr_nota_incertezas(D[f'Operação {j+1}']))
    # Agora estão ordenadas por prioridade


## Decisão
W = {}
Disponíveis = []
Op_Disponíveis = []

for i in range(len(E)):
    f = E[f'{i}']
    if f['Equipamento'] == '0':
        k = 0
        for n in lista_caminhões:
            b = 0
            a = n['Matriz de carga']
            for i in range(len(a)):
                if (f['Carga da operação'] <= a[i][0]) and (f['Altura da operação'] <= a[i][1]) and (f['Raio da operação'] <= a[i][2]):
                    if b == 0:
                        Disponíveis.append(lista_caminhões[k])
                        b = b + 1
            k = k + 1
        notas = list(map(cam_nota_final, Disponíveis))
        a = 0
        while a == 0:
            for j in Disponíveis:
                if cam_nota_final(j) == máximo(notas):
                    W[f'{i}'] = [f['Nome'], j['Nome']]
                    lista_caminhões.remove(j)
                    a = 1
    if f['Equipamento'] != '0':
        W[f'{i}'] = [f['Nome'], f['Equipamento']]
        lista_caminhões.remove(f['Equipamento'])
    if f['Operador'] == '0':
        if aux == 1:
            # Operador não precisa necessariamente dirigir
            for n in lista_operadores:
                if W[f'{i}'][1] in n['Caminhões']:
                    Op_Disponíveis.append(n)
        if aux != 1:
            # Operador precisa dirigir
            for n in lista_operadores:
                if W[f'{i}'][1] in n['Caminhões'] and n['Pode dirigir'] == True:
                    Op_Disponíveis.append(n)
        notas_op = list(map(op_nota_final, Op_Disponíveis))
        a = 0
        while a == 0:
            for j in Op_Disponíveis:
                if op_nota_final(j) == máximo(notas_op):
                    W[f'{i}'].append(j['Nome'])
                    lista_operadores.remove(j)
                    a = 1
    if f['Operador'] != '0':
        W[f'{i}'].append(f['Operador'])
        lista_operadores.remove(f['Operador'])
    if aux == 1 and f['Auxiliar'] == '0':
        notas_aux = list(map(op_nota_avaliação, lista_auxiliares))
        a = 0
        while a == 0:
            for j in lista_auxiliares:
                if op_nota_avaliação(j) == máximo(notas_aux):
                    W[f'{i}'].append(j)
                    lista_auxiliares.remove(j)
                    a = 1
    if aux == 1 and f['Auxiliar'] != '0':
        W[f'{i}'].append(f['Auxiliar'])
        lista_auxiliares.remove(f['Auxiliar'])
    Disponíveis = []
    Op_Disponíveis = []

for i in range(N):
    print(W[f'{i}\n'])

    

    
        
