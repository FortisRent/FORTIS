// No cadastro do FIGMA, tem um campo "gostaria de informar outro número de telefone?" - Criar tabela adicional?

package org.fortis.model;

import lombok.Data;

@Data
public class UserModel {
    private Integer id;
	private String uuid;
	private String name;
	private String phone;
	private String email;
	private String cpf;
	private String password;
	private String created_at;
}